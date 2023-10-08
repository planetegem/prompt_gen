let components, current, decision, filter,
    constructingPrompt = false;

function resetAll(){
    current = 0;
    fusing = false;
    components = [];
    requests = 0;
    resetFilters();
    standardProbabilities();
}
function getPrompt(){
    console.log("START");
    if (!constructingPrompt){
        constructingPrompt = true;
        resetAll();
        retrieveWord("noun");
    }
}
function retrieveWord(request, condition = "any"){
    requests++;
    let http = new XMLHttpRequest();
    console.log(request + " " + condition);
    http.onreadystatechange = function(){
        if (this.readyState == 4 && this.status == 200) {
            buildPrompt(this, request);
        }
    };
    http.open("GET", "/demo/prompt_builder/assets/promptGenerator/retrieveWord.php?request="+request+"&condition="+condition, true);
    http.send();
}

function buildPrompt(http, type){
    // A. CREATE NOUN
    if (type === "noun"){
        let noun = JSON.parse(http.responseText),
            form, string;
        
        if (noun.type === "plural" || noun.countable === "false"){
            form = "plural";
            string = noun.singular;
        } else if (noun.plural === ""){
            form = "singular";
            string = noun.singular;
        } else {
            decision = decideValue(probabilities.nounNumber);
            if (decision === "plural"){
                form = "plural";
                string = noun.plural;
            } else {
                form = "singular";
                string = noun.singular;
            }
        }
        countable = JSON.parse(noun.countable);
        components[current] = new nounGroup(string, form, noun.article, noun.type, countable);
    
         // REQUEST ADJECTIVE
        decision = decideValue(probabilities.getAdjective);
        if (decision && components[current].article != "definite"){
            filter = filterAdjective(components[current].attribute);
            
            retrieveWord("adjective", filter);
            return;
        }
    }
    // B. ADD ADJECTIVE TO STRING
    if (type === "adjective"){
        let adjective = JSON.parse(http.responseText),
            string;
    
        // DECISION TREE
        decision = decideValue(probabilities.typeAdjective);
        switch (decision){
            case "single":
                string = adjective[0].entry + " ";
                break;
            case "double":
                if (adjective[0].type === adjective[1].type){
                    if (adjective[0].type === "color"){
                        string = adjective[0].entry + " and " + adjective[1].entry + " ";
                    } else {
                        string = adjective[0].entry + " ";
                    }
                } else {
                    string = adjective[0].entry + ", " + adjective[1].entry + " ";
                }
                break;
            case "magnified":
                string = adjective[2].entry + " " + adjective[0].entry + " ";
                requests++;
                break;
            default:
                string = "";
        }
        components[current].string = string + components[current].string;
    }
    // B2. EXCEPTION BRANCHES DETERMINED BY NOUN (EVENT & FUSION)
    if (components[current] instanceof nounGroup){
        if (components[current].attribute === "event" || components[current].attribute === "fusion" || fusing){
            nounExceptions();
            return;
        }
    }
    calculateProbabilities();
    
    // C. IF NOUN OR ADJECTIVE, DECIDE ON QUANTIFIER
    if ((type === "noun" || type === "adjective") && components[current].form === "plural"){
        decision = decideValue(probabilities.getQuantifier);
        
        if (decision === "simple"){
            retrieveWord("quantifier", "filter~allow~" + decision);
            return;
        } else if (decision != "none") {
            filter = filterQuantifier(components[current].attribute, components[current].countable);
            retrieveWord("quantifier", filter);
            return;
        }
    }
    // D. ADD QUANTIFIER TO STRING
    if (type === "quantifier"){
        let quantifier = JSON.parse(http.responseText),
            string;
        
        // 2. DECIDE PLURAL OR SINGULAR
        if (quantifier.type === "plural" || quantifier.type === "simple"){
            string = quantifier.singular;
            components[current].form = "plural";
            components[current].article = "none";
        } else if (quantifier.plural === ""){
            string = quantifier.singular;
            components[current].form = "singular";
            components[current].article = "indefinite";
        } else {
            decision = decideValue(probabilities.quantifierNumber);
            if (decision === "plural"){
                string = quantifier.plural;
                components[current].form = "plural";
                components[current].article = "none";
            } else {
                string = quantifier.singular;
                components[current].form = "singular";
                components[current].article = "indefinite";
            }
        }
        components[current].string = string + " " + components[current].string;
        
        // 3. CONSIDER ADDING QUALIFIER TO QUANTIFIER
        if (quantifier.type === "container" || quantifier.type === "group" || quantifier.type === "collection"){
            retrieveWord("adjective", "filter~allow~size");
            return;
        }
    }
    // E. IF CURRENT COMPONENT IS NOUNGROUP, ADD ARTICLE
    if (components[current] instanceof nounGroup && components[current].form === "singular"){
        addArticle(components[current]);
    }
    calculateProbabilities();

    // G. DECIDE IF CONNECTOR IS REQUIRED
    if (current === 0){
        decision = decideValue(probabilities.getNoun);
        if (decision){
            filter = filterConnector(components[current].attribute);
            current++;
            retrieveWord("connector", filter);
            return;
        }
    }
    // H. ADD CONNECTOR
    if (type === "connector"){
        let connector = JSON.parse(http.responseText);
        components[current] = new connectorGroup(connector.main, connector.adverb, connector.passive, connector.req, connector.type);
        components[current].adverb = JSON.parse(components[current].adverb);
        components[current].passive = JSON.parse(components[current].passive);
        
        // 1. SPECIAL REQUIREMENTS
        if (components[current].req === ""){
            components[current].req = components[current].attribute;
        }
        switch (components[current].req){
            case "plural":
                probabilities.nounNumber = [{value: "plural", weight: 100}];
                components[current].req = components[current].attribute;
                break;
            case "singular":
                probabilities.nounNumber = [{value: "singular", weight: 100}];
                components[current].req = components[current].attribute;
                break;
            case "liquid":
                probabilities.getQuantifier = [{value: "simple", weight: 20}, {value: "any", weight: 40}, {value: "none", weight: 60}];
                components[current].attribute = components[current].req;
                quantifierKey.liquid = "filter~allow~liquid";
                break;
            case "made":
                probabilities.nounNumber = [{value: "plural", weight: 100}];
                probabilities.getQuantifier = [{value: "simple", weight: 0}, {value: "any", weight: 0}, {value: "none", weight: 100}];
                break;
            case "body":
                probabilities.getAdjective = [{value: false, weight: 0}, {value: true, weight: 100}];
            default:
                if (components[0].form === "plural"){
                    probabilities.nounNumber = [{value: "singular", weight: 100}, {value: "plural", weight: 0}];
                } else {
                    probabilities.getQuantifier = [{value: "simple", weight: 70}, {value: "any", weight: 20}, {value: "none", weight: 10}];
                    probabilities.nounNumber = [{value: "singular", weight: 95}, {value: "plural", weight: 5}];
                }
        }
        // 2. CONSIDER ADVERB
        if (components[current].adverb){
            decision = decideValue(probabilities.adverb);
            if (decision){
                filter = filterAdverb(components[current].attribute);
                components[current].attribute = components[current].req;
                retrieveWord("adverb", filter);
                return;
            }
        }
        components[current].attribute = components[current].req;
    }
    // I. ATTACH ADVERB
    if (type === "adverb"){
        let adverb = JSON.parse(http.responseText);
        components[current].string = adverb.entry + " " + components[current].string;
    }
    // J. FINISH CONNECTOR
    if (components[current] instanceof connectorGroup){
        // 1. CONSIDER MAKING PASSIVE
        if (components[current].passive){
            decision = decideValue(probabilities.passive);
            if (decision){
                components[current].string = "being " + components[current].string;
            }
        }
        // 2. REQUEST NEW NOUN IF TRANSITIVE
        if (components[current].attribute != "transit"){
            filter = filterNoun(components[current].attribute);
            current++;
            retrieveWord("noun", filter);
            return;
        }
    }
    // K. PRINT FINAL PROMPT
    result = components[0].string;
    for (let i = 1; i < components.length; i++){
        result += " " + components[i].string;
    }
    constructingPrompt = false;
    console.log(result);
    document.getElementById("targetField").innerHTML = result;
}

// SPECIAL BRANCHES
let fusing = false;

function nounExceptions(){
    // A. EVENT BRANCH
    if (components[current].attribute === "event"){
        components[current].article = "definite";
        addArticle(components[current]);
        components[current].string += " of";

        probabilities.nounNumber = [{value: "singular", weight: 100}, {value: "plural", weight: 0}];
        probabilities.quantifierNumber = [{value: "singular", weight: 100}, {value: "plural", weight: 0}];
        probabilities.getQuantifier = [{value: "simple", weight: 100}, {value: "any", weight: 0}, {value: "none", weight: 0}];

        filter = filterNoun(components[current].attribute);
        current++;
        retrieveWord("noun", filter);
        return;
    }
    // B. FUSION BRANCH
    if (components[current].attribute === "fusion"){
        fusing = true;
        probabilities.nounNumber = [{value: "singular", weight: 100}];
        probabilities.getAdjective = [{value: false, weight: 100}];
        probabilities.article = [{value: "indefinite", weight: 100}];
        components[current].article = "definite";
        addArticle(components[current]);
        components[current].string += " of";
        
        filter = filterNoun(components[current].attribute);
        current++;
        retrieveWord("noun", filter);
        return;
    }
    if (fusing){
        fusing = false;
        addArticle(components[current]);
        components[current].string += " and";
        current++;
        retrieveWord("noun", "filter~allow~" + components[current-1].attribute);
        return;
    }
}

function addArticle(nounGroup){
    // DECIDE WHICH ARTICLE
    if (nounGroup.article === "any"){
        nounGroup.article = decideValue(probabilities.article);
    }
    // ATTACH ARICLE
    switch (nounGroup.article){
        case "definite":
            nounGroup.string = "the " + nounGroup.string;
            break;
        case "indefinite":
            const regEx = /^[aeuio]/,
                  strongVowels = [/^used/, /^uni/];
            let strongVowel = false;
                  
            for(exception of strongVowels){
                if(exception.test(nounGroup.string)){
                    strongVowel = true;
                }
            } 
            
            if (strongVowel){
                nounGroup.string = "a " + nounGroup.string;
            } else if (regEx.test(nounGroup.string)){
                nounGroup.string = "an " + nounGroup.string;
            } else {
                nounGroup.string = "a " + nounGroup.string;
            }
            break;
    }
}
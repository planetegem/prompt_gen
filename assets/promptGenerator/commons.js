let requests = 0; // 

// PROBABILITY MATRIX: DECIDE ODDS TO ADD WORDS
let probabilities = {};
function standardProbabilities(){
    probabilities.nounNumber = [{value: "singular", weight: 90}, {value: "plural", weight: 10}];
    probabilities.quantifierNumber = [{value: "singular", weight: 95}, {value: "plural", weight: 5}];
    probabilities.getQuantifier = [{value: "simple", weight: 75}, {value: "any", weight: 25}, {value: "none", weight: 0}];
    probabilities.article = [{value: "definite", weight: 0}, {value: "indefinite", weight: 100}, {value: "none", weight: 0}];
    probabilities.adverb = [{value: false, weight: 0}, {value: true, weight: 100}];
    probabilities.passive = [{value: false, weight: 0}, {value: true, weight: 100}];
    calculateProbabilities();  // variable probabilities
}
function calculateProbabilities(){
    probabilities.getAdjective = [{value: false, weight: 50 + requests*7}, {value: true, weight: 50 - requests*7}];
    probabilities.typeAdjective = [{value: "single", weight: 91 + requests*2}, {value: "double", weight: 6 - requests}, {value: "magnified", weight: 3 - requests}];
    probabilities.getNoun = [{value: false, weight: 25 + requests*10}, {value: true, weight: 75 - requests*10}];
}
// LOAD IN PROBABILITY ARRAY TO DECIDE OUTCOME
function decideValue(array){
    let randNumber = Math.floor(Math.random()*100) + 1,
        currentWeight = 0; // ROLL NUMBER BETWEEN 1 & 100
    
    for (let i = 0; i < array.length; i++){
        if (randNumber > array[i].weight + currentWeight){
            currentWeight += array[i].weight;
        } else {
            return array[i].value;
        }
    }
}

// FILTERS: DECIDE WHAT TO REQUEST BASED ON KEY
let nounKey = {}, adjectiveKey = {}, quantifierKey = {}, connectorKey = {}, adverbKey = {};

// NOUN FILTERS: TYPE OF NOUN TO REQUEST BASED ON CONNECTOR
function fillNounFilters(){
    nounKey.event = "filter~forbid~liquid~noncount~object~event~fusion";
    nounKey.fusion = "filter~forbid~event~fusion~liquid~noncount~place";
    nounKey.count = "filter~forbid~liquid~noncount~event~fusion";
    nounKey.liquid = "filter~allow~liquid";
    nounKey.solid = "filter~forbid~liquid~fusion~event";
    nounKey.simple = "filter~forbid~event~fusion";
    nounKey.general = "filter~forbid~event~fusion";
    nounKey.cover = "filter~forbid~event~fusion";
    nounKey.large = "filter~forbid~event~fusion~liquid";
    nounKey.small = "filter~allow~object~food";
    nounKey.sentient = "filter~allow~creature";
    nounKey.made = "filter~allow~noncount";
    nounKey.body = "filter~allow~body";
    nounKey.vehicle = "filter~allow~vehicle";
    nounKey.object = "filter~forbid~creature~event~liquid~noncount~fusion";
    nounKey.place = "filter~allow~place";
}
function filterNoun(key){
    
    nounKey.fuse = "filter~allow~" + components[0].attribute;
    
    if ([key] in nounKey){
        return nounKey[key];
    } else {
        return "any";
    }
}
// ADJECTIVE FILTERS: TYPE OF ADJECTIVE TO REQUEST BASED ON NOUN
function fillAdjectiveFilters(){
    adjectiveKey.body = "filter~forbid~emotion~glory~liquid~wealth";
    adjectiveKey.creature = "filter~forbid~environs~glory~liquid~shape~nonsentient~body~humidity";    
    adjectiveKey.event = "filter~allow~glory~magic";
    adjectiveKey.food = "filter~forbid~emotion~environs~glory~liquid~period~body";
    adjectiveKey.furniture = "filter~forbid~emotion~build~humidity~liquid~glory~texture~body";
    adjectiveKey.liquid = "filter~allow~liquid~magic~color~wealth";
    adjectiveKey.nature = "filter~forbid~emotion~build~liquid~glory~period~wealth~body";
    adjectiveKey.noncount = "filter~allow~color~magic~humidity~wealth";
    adjectiveKey.place = "filter~allow~size~period~color~magic~nonsentient~humidity~period~wealth";
    adjectiveKey.object = "filter~forbid~build~emotion~environs~glory~humidity~liquid~texture~body";
    adjectiveKey.vehicle = "filter~forbid~build~emotion~environs~glory~humidity~liquid~texture~body";
    adjectiveKey.fusion = "filter~allow~glory~magic";
}
function filterAdjective(key){
    if ([key] in adjectiveKey){
        return adjectiveKey[key];
    } else {
        return "any";
    }
}
// QUANTIFIER FILTERS: TYPE OF QUANTIFIER TO REQUEST BASED ON NOUN
function fillQuantifierFilters(){
    quantifierKey.liquid = "filter~forbid~count~group~plural";
    quantifierKey.creature = "filter~forbid~noncount~container";
    quantifierKey.vehicle = "filter~forbid~noncount~container~group~liquid";
    quantifierKey.container = "filter~allow~simple";
    quantifierKey.noncount = "filter~forbid~count~container~liquid~group~food~plural";
    quantifierKey.body = "filter~forbid~noncount~container~group~food";
    quantifierKey.furniture = "filter~forbid~noncount~container~group~liquid~food";
    quantifierKey.place = "filter~forbid~noncount~container~group~liquid~food";
    quantifierKey.object = "filter~forbid~noncount~container~group~food";
}
function filterQuantifier(key, allowed){
    let countable = allowed ? "noncount" : "count";
    
    quantifierKey.food = "filter~forbid~" + countable +"~group~liquid~container";
    quantifierKey.nature = "filter~forbid~" + countable + "~container~group~liquid";
    if ([key] in quantifierKey){
        return quantifierKey[key];
    } else {
        return "any";
    }
}
// CONNECTOR FILTERS: TYPE OF CONNECTOR TO REQUEST BASED ON NOUN
function fillConnectorFilters(){
    connectorKey.body = "filter~forbid~sentient~vehicle~life~liquid";
    connectorKey.creature = "filter~forbid~made~vehicle~object~life~liquid";
    connectorKey.food = "filter~forbid~sentient~made~vehicle~life~liquid";
    connectorKey.furniture = "filter~forbid~small~sentient~vehicle~life~liquid";
    connectorKey.liquid = "filter~forbid~sentient~large~made~small~vehicle~object~life~cover";
    connectorKey.nature = "filter~forbid~sentient~made~vehicle~liquid";
    connectorKey.noncount = "filter~forbid~sentient~made~cover~vehicle~object~life~liquid";
    connectorKey.place = "filter~forbid~sentient~small~made~vehicle~life~liquid";
    connectorKey.object = "filter~forbid~sentient~cover~vehicle~life~liquid";
    connectorKey.vehicle = "filter~forbid~sentient~cover~small~life~liquid";
}
function filterConnector(key){
    if ([key] in connectorKey){
        return connectorKey[key];
    } else {
        return "any";
    }
}
// ADVERB FILTERS: TYPE OF ADVERB TO REQUEST BASED ON CONNECTOR
function fillAdverbFilters(){
    adverbKey.cover = "filter~allow~magnifier";
    adverbKey.sentient = "filter~allow~manner";
    adverbKey.large = "filter~allow~manner";
    adverbKey.small = "filter~allow~manner";
    adverbKey.general = "filter~allow~magnifier";
}
function filterAdverb(key){
    if ([key] in adverbKey){
        return adverbKey[key];
    } else {
        return "any";
    }
}
// RESET ALL FILTERS (WHEN REQUESTING PROMPT)
function resetFilters(){
    fillNounFilters();
    fillAdjectiveFilters();
    fillQuantifierFilters();
    fillConnectorFilters();
    fillAdverbFilters();
}
// CONSTRUCTORS
class nounGroup {
    constructor(string, form, article, attribute, countable){
        this.string = string; // FINISHED GROUP (ATTIBUTE + QUANTIFIER + QUALIFIER + NOUN)
        this.form = form; // SINGULAR OR PLURAL
        this.article = article;
        this.attribute = attribute; // SPECIAL ATTRIBUTE I.E. TRANSITIVE
        this.countable = countable;
    }
}
class connectorGroup {
    constructor(string, adverb, passive, req, attribute){
        this.string = string;
        this.adverb = adverb;
        this.attribute = attribute; 
        this.passive = passive;
        this.req = req; // SPECIAL REQUIREMENTS I.E. NEXT NOUNGROUP PLURAL
    }
}

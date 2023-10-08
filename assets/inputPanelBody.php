<div id="inputPanel" <?php if (isset($_SESSION["connecting"])){echo 'class="slideIn"';}?>>
    <form id="nouns" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="header">
            <div class="inputLeft">
                <img src="assets/images/leftArrow.svg">
            </div>
            <h2>nouns</h2>
            <div class="inputRight">
                <img src="assets/images/rightArrow.svg">
            </div>
        </div>
        <p class="intro">Insert nouns or update existing ones. Use type if special rules apply. Update type to 'delete' to delete an existing entry.</p>
        <div class="mainForm">
            <label for="singular">singular:</label>
            <input type="text" name="singular" id="singular" size="15">
            <label for="plural">plural:</label>
            <input type="text" name="plural" id="plural" size="15">
        </div>
        <div class="subForm">
            <div>
                <h3>specifics</h3>
                <div>
                    <label class="checkbox" for="definite">
                        <input type="checkbox" name="definite" id="definite" value ="definite" checked>
                        <div>definite</div>
                    </label>
                    <label class="checkbox" for="indefinite">
                        <input type="checkbox" name="indefinite" id="indefinite" value ="indefinite" checked>
                        <div>indefinite</div>
                    </label>
                    <label class="checkbox" for="countable">
                        <input type="checkbox" name="countable" id="countable" value ="countable" checked>
                        <div>countable</div>
                    </label>
                </div>
            </div>
            <div>
                <h3>type</h3>
                <input type="text" name="nounType" <?php if (isset($_SESSION["nounType"])){echo 'value="' . $_SESSION["nounType"] . '"';} else {echo "value=default";}?> id="nounType" size="6">
            </div>
        </div>
        <input type="submit" name="noun" value="add noun" class="button">
        <div class="submitStatus"><?php echo $_SESSION["feedback"];?></div>
    </form>
    
    <form id="qualifiers" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="header">
            <div class="inputLeft">
                <img src="assets/images/leftArrow.svg">
            </div>
            <h2>qualifiers</h2>
            <div class="inputRight">
                <img src="assets/images/rightArrow.svg">
            </div>
        </div>
        <p class="intro">Qualifiers can be attached to nouns or verbs to add color. Use type if special rules apply. Update type to 'delete' to delete an existing entry.</p>
        <div class="mainForm">
            <label for="qualEntry">entry</label>
            <input type="text" name="qualEntry" id="qualEntry" size="15">
        </div>
        <div class="subForm">
            <div>
                <h3>form</h3>
                <div>
                    <label class="checkbox" for="adjective">
                        <input type="radio" name="form" id="adjective" value="adjective" checked>
                        <div>adjective</div>
                    </label>
                    <label class="checkbox" for="adverb">
                        <input type="radio" name="form" id="adverb" value ="adverb">
                        <div>adverb</div>
                    </label>
                </div>
            </div>
            <div>
                <h3>type</h3>
                <input type="text" name="qualType" value="default" id="qualType" size="6">
            </div>
        </div>
        <input type="submit" name="qualifier" value="add qualifier" class="button">
        <div class="submitStatus"><?php echo $_SESSION["feedback"];?></div>
    </form>
    <form id="quantifiers" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="header">
            <div class="inputLeft">
                <img src="assets/images/leftArrow.svg">
            </div>
            <h2>quantifiers</h2>
            <div class="inputRight">
                <img src="assets/images/rightArrow.svg">
            </div>
        </div>
        <p class="intro">Quantifiers can be added to nouns to signify a quantity.</p>
        <div class="mainForm">
            <label for="quantSingular">singular</label>
            <input type="text" name="quantSingular" id="quantSingular" size="15">
            <label for="quantPlural">plural</label>
            <input type="text" name="quantPlural" id="quantPlural" size="15">
        </div>
        <div class="subForm">
            <div>
                <h3>allowed with</h3>
                <div>
                    <label class="checkbox" for="count">
                        <input type="checkbox" name="count" id="count" value="count" checked>
                        <div>count nouns</div>
                    </label>
                    <label class="checkbox" for="noncount">
                        <input type="checkbox" name="noncount" id="noncount" value ="noncount" checked>
                        <div>noncount nouns</div>
                    </label>
                </div>
            </div>
            <div>
                <h3>type</h3>
                <input type="text" name="quantType" value="default" id="quantType" size="6">
            </div>
        </div>
        <input type="submit" name="quantifier" value="add quantifier" class="button">
        <div class="submitStatus"><?php echo $_SESSION["feedback"];?></div>
    </form>
    <form id="connectors" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="header">
            <div class="inputLeft">
                <img src="assets/images/leftArrow.svg">
            </div>
            <h2>connectors</h2>
            <div class="inputRight">
                <img src="assets/images/rightArrow.svg">
            </div>
        </div>
        <p class="intro">Connectors add noun groups to each other. Use the 2nd input field if the connector needs to be divided by an adverb.</p>
        <div class="mainForm">
            <label for="mainEntry">entry</label>
            <input type="text" name="mainEntry" id="mainEntry" size="18">
        </div>
        <div class="subForm">
            <div>
                <h3>specifics</h3>
                <div class="mainForm">
                    <label for="connectorType">type</label>
                    <input type="text" name="connectorType" id="connectorType" size="6" value="default">
                    <label for="connectorRequirement">need</label>
                    <input type="text" name="connectorRequirement" id="connectorRequirement" size="6">
                </div>
            </div>
            <div>
                <h3>allowed</h3>
                <div>
                    <label class="checkbox" for="adverb">
                        <input type="checkbox" name="adverb" id="adverb" value="adverb">
                        <div>adverb</div>
                    </label>
                    <label class="checkbox" for="passive">
                        <input type="checkbox" name="passive" id="passive" value ="passive">
                        <div>passive</div>
                    </label>
                </div>
            </div>
        </div>
        <input type="submit" name="connector" value="add connector" class="button">
        <div class="submitStatus"><?php echo $_SESSION["feedback"];?></div>
    </form>
</div>
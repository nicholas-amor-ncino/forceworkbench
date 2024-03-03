<?php
require_once 'session.php';
require_once 'shared.php';

if (isset($_GET['actionJump']) && $_GET['actionJump'] != basename($_SERVER['PHP_SELF'])) {
    header("Location: $_GET[actionJump]");
    exit;
}

include_once 'header.php';

if (isset($_GET['select'])) {
    displayError("Choose an object and an action to which to jump.");
}
?>

<form method="get" action="">
    <p class='instructions'>Select an action to perform:</p>

    <p>
        <label for="actionJump"><strong>Jump to: </strong></label>
        <select name='actionJump' id='actionJump' style='width: 20em;' onChange='toggleObjectSelectDisabled();'>
            <option value='select.php'></option>
            <?php
            foreach ($GLOBALS["MENUS"] as $menu => $pages) {
                foreach ($pages as $href => $page) {
                    if ($page->onMenuSelect) print "<option value='" . $href . "'>" . $page->title . "</option>";
                }
            }
            ?>
        </select>
    </p>

    <p>
        <label for="default_object"><strong>Object: &nbsp; </strong></label>
        <?=ObjectSelection(WorkbenchContext::get()->getDefaultObject(), 'default_object'); ?>
    </p>
    
    <input type='submit' name='select' value='Select'/>
</form>

<script type="text/javascript">
    function toggleObjectSelectDisabled() {
        var usesObject = new Array();
        <?php
        foreach ($GLOBALS["MENUS"] as $menu => $pages) {
            foreach ($pages as $href => $page) {
                if ($page->onMenuSelect === 'usesObject') {
                    print "usesObject['$href'] = '$href';\n";
                }
            }
        }
        ?>

        var actionJumpVal = document.getElementById('actionJump').value;

        if (usesObject[actionJumpVal] != undefined) {
            document.getElementById('default_object').disabled = false;
        } else {
            document.getElementById('default_object').disabled = true;
        }
    }
</script>

<?php
addFooterScript("<script type='text/javascript'>toggleObjectSelectDisabled();</script>");
include_once 'footer.php';
?>

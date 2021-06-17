<?php
if (!empty($_POST["submit"])) {
    $siteUrl = $_POST["site_url"];

    include_once('ScriptTagFinder.php');

    $myScriptTags = new ScriptTagFinder($siteUrl);
    $myScriptTags->baseUrl = $siteUrl;
    $MainPageScripts = $myScriptTags->getScriptTags($siteUrl, "HOME PAGE");
    $InnerPageScripts = $myScriptTags->crawlPages($siteUrl);
    $isInvalidUrl =  $myScriptTags->invalidUrl;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Script tags finder</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" integrity="undefined" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="undefined" crossorigin="anonymous"></script>
</head>

<body>
    <div class="pt-4 m-2">
    <h4>SCRIPT TAGS FINDER</h4>
        <form method="post" action="">
            <div class="row justify-content-center">
                <div class="form-group   align-center ">
                    URL:
                    <input type="text" name="site_url" placeholder="Enter URL" required>
                    <button type="submit" name="submit" value="submit">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <hr>

    <div class="container-fluid b-3">
        <?php
        if (isset($MainPageScripts) && !empty($MainPageScripts)) {
        ?>
            <h3 class="text-primary"><?= !empty($MainPageScripts['Level']) ? $MainPageScripts['Level'] : "" ?></h3>
            <h5 class="text-primary">Page URL: <?= !empty($MainPageScripts['Page']) ? $MainPageScripts['Page'] : "" ?></h5>
            <?php
            if (isset($MainPageScripts['ScriptTagList']) && !empty($MainPageScripts['ScriptTagList'])) {
                foreach ($MainPageScripts['ScriptTagList'] as $key => $ScriptTagList_Item) {
            ?>
                    <p><?= $key + 1 ?>) <?= !empty($ScriptTagList_Item) ? htmlspecialchars("<script " . $ScriptTagList_Item . "</script>") : "" ?></p>
            <?php
                }
            }
            ?>
            <?php
        } else {
            if (!empty($_POST["submit"])) {
                if ($isInvalidUrl) {
            ?>
                    <h4 class="text-danger">Invalid URL</h4>
                <?php
                } else {
                ?>
                    <h4 class="text-danger">Page not found</h4>
        <?php
                }
            }
        }
        ?>
    </div>


    <div class="container-fluid">
        <?php
        if (isset($InnerPageScripts) && !empty($InnerPageScripts)) {
            foreach ($InnerPageScripts as $InnerPageScripts_items) {
        ?>
                <hr>
                <h3 class="text-success"><?= !empty($InnerPageScripts_items['Level']) ? $InnerPageScripts_items['Level'] : "" ?></h3>
                <h5 class="text-success">Page URL: <?= !empty($InnerPageScripts_items['Page']) ? $InnerPageScripts_items['Page'] : "" ?></h5>
                <?php
                if (isset($InnerPageScripts_items['ScriptTagList']) && !empty($InnerPageScripts_items['ScriptTagList'])) {
                    foreach ($InnerPageScripts_items['ScriptTagList'] as $key => $ScriptTagList_Item) {
                ?>
                        <p><?= $key + 1 ?>) <?= !empty($ScriptTagList_Item) ? htmlspecialchars("<script" . $ScriptTagList_Item . "</script>") : "" ?></p>
                    <?php
                    }
                }
            }
        } else {
            if (!empty($_POST["submit"])) {
                if (!$isInvalidUrl) {
                    ?>
                    <hr>
                    <h4 class="text-danger">Inner pages not found</h4>
        <?php
                }
            }
        }
        ?>
    </div>
</body>

</html>
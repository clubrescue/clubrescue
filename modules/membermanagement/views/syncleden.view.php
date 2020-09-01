<?php include $_SERVER['DOCUMENT_ROOT'] . '/clubredders/header.php'; ?>
<main>
    <div class="container">
        <?php if (current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') || current_user_can('contributor')) {
    echo $message; ?>
        <form id="form" action="" method="post">
            <button type="submit" name="sync" class="btn waves-effect waves-light cr-button">Synchroniseer Office365
                en
                ClubRedders.<i class="material-icons right">sync_alt</i></button>
        </form>

        <?php
} ?>
</main>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/clubredders/footer.php';

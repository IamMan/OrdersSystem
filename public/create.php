<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link href="content/css/bootstrap.min.css" rel="stylesheet">
    <script src="content/js/bootstrap.min.js"></script>
</head>

<body>
<div class="container">

    <div class="span10 offset1">
        <div class="row">
            <h3>Create a Customer</h3>
        </div>

        <form class="form-horizontal" action="create.php" method="post">
            <div class="control-group <?php echo !empty($titleError) ? 'error' : ''; ?>">
                <label class="control-label">title</label>

                <div class="controls">
                    <input name="title" type="text" placeholder="Title" value="<?php echo !empty($title) ? $title : ''; ?>">
                    <?php if (!empty($titleError)): ?>
                        <span class="help-inline"><?php echo $titleError; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="control-group <?php echo !empty($textError) ? 'error' : ''; ?>">
                <label class="control-label">Description</label>
                <div class="controls">
                    <textarea name="text" placeholder="Description">
                        <?php echo !empty($text) ? $text : ''; ?>
                    </textarea>
                    <?php if (!empty($textError)): ?>
                        <span class="help-inline"><?php echo $textError; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="control-group <?php echo !empty($costError) ? 'error' : ''; ?>">
                <label class="control-label">Price</label>

                <div class="controls">
                    <input name="cost" type="text" placeholder="Price"
                           value="<?php echo !empty($cost) ? $cost : ''; ?>">
                    <?php if (!empty($costError)): ?>
                        <span class="help-inline"><?php echo $costError; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Create</button>
                <a class="btn" href="index.php">Back</a>
            </div>
        </form>
    </div>
</div>
<!-- /container -->
</body>
</html>
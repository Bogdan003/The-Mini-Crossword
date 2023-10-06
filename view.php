<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>The Mini Crossword</h1>
        <br>
        <div class="clue-box">
        </div>
        <table>
            <tbody>
                <?php foreach ($additionalCellsData as $row => $word) { ?>
                    <tr>
                        <?php foreach ($word as $col => $letter) { ?>
                            <td>
                                <div class="word-index-box">
                                    <p class="word-index"><?php if (isset($letter['number'])) echo $letter['number']; ?></p>
                                </div>
                                <div class="word-box" data-across-cell="">
                                    <p><?php echo $letter['value']; ?></p>
                                </div>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

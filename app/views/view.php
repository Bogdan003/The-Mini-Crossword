<!DOCTYPE html>
<head>
    <link rel="shortcut icon" type="image/x-icon" href="https://www.nytimes.com/games-assets/v2/metadata/nyt-favicon.ico?v=v2310121315"/>
    <link rel="stylesheet" type="text/css" href="/assets/styles.css">
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
                            <td class="<?php if ($letter['value'] == '-') echo 'black-box'; ?>">
                                <div class="word-index-box">
                                    <p class="word-index"><?php if (isset($letter['number'])) echo $letter['number']; ?></p>
                                </div>
                                <div class="word-box <?php if ($letter['value'] == '-') echo 'black-box'; ?>" data-across-cell="">
                                    <p><b><?php echo $letter['value']; ?></b></p>
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

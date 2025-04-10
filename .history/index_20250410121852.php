<?php
// HTMLファイルを再帰的に探す関数
function findHtmlFiles($dir) {
    $htmlFiles = array();
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === 'index.php') {
            continue;
        }
        
        $path = $dir . '/' . $file;
        $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
        $relativePath = ltrim($relativePath, '/\\');
        
        if (is_dir($path)) {
            // ディレクトリの場合は再帰的に検索
            $htmlFiles = array_merge($htmlFiles, findHtmlFiles($path));
        } else if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
            // HTMLファイルの場合は配列に追加
            $htmlFiles[] = array(
                'path' => $relativePath,
                'name' => $file,
                'folder' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $dir)
            );
        }
    }
    
    return $htmlFiles;
}

// 現在のディレクトリからHTMLファイルを検索
$htmlFiles = findHtmlFiles(dirname(__FILE__));

// ファイルをフォルダごとにグループ化
$filesByFolder = array();
foreach ($htmlFiles as $file) {
    $folder = dirname($file['path']);
    if (!isset($filesByFolder[$folder])) {
        $filesByFolder[$folder] = array();
    }
    $filesByFolder[$folder][] = $file;
}

// フォルダを名前順にソート
ksort($filesByFolder);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTMLファイル一覧</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        a {
            color: #0066cc;
            text-decoration: none;
            display: block;
        }
        a:hover {
            text-decoration: underline;
        }
        .folder {
            font-weight: bold;
            margin-top: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .no-files {
            text-align: center;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>HTMLファイル一覧</h1>
    
    <?php if (empty($filesByFolder)): ?>
        <p class="no-files">HTMLファイルが見つかりませんでした。</p>
    <?php else: ?>
        <?php foreach ($filesByFolder as $folder => $files): ?>
            <div class="folder"><?php echo empty($folder) ? '/' : $folder; ?></div>
            <ul>
                <?php foreach ($files as $file): ?>
                    <li>
                        <a href="<?php echo $file['path']; ?>"><?php echo htmlspecialchars($file['name']); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
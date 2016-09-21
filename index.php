<?php
$metadata = file_get_contents('http://epsilon.shoutca.st:8505/stats');
$metadata=simplexml_load_string($metadata);
header('Content-Type: text/xml');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $botListeners = $_POST["listeners"];
  $file = fopen("metadata.json", "w") or die("Unable to open file!");
  $products = array(
                  'botListeners'    => $botListeners,
                  'updatedTime'    => date('Y-m-d H:i:s')
              );
  $jsonProducts = json_encode($products);
  fwrite($file, $jsonProducts);
  fclose($file);
}

$file = json_decode(file_get_contents("metadata.json"));
$fileUpdateStamp = $file->updatedTime;
$fileBotListeners = $file->botListeners;

$timeNow = new DateTime(date('Y-m-d H:i:s'));
$updateTimeLimit = new DateTime(date($fileUpdateStamp));
$updateTimeLimit->add(new DateInterval('PT30S'));

if ($timeNow < $updateTimeLimit) {
    $listeners = $metadata->CURRENTLISTENERS + $fileBotListeners;
    $ulisteners = $metadata->UNIQUELISTENERS + $fileBotListeners;
} else {
    $listeners = $metadata->CURRENTLISTENERS;
    $ulisteners = $metadata->UNIQUELISTENERS;
}

$metadata->CURRENTLISTENERS = $listeners;
$metadata->UNIQUELISTENERS = $ulisteners;
echo $metadata->asXml();
?>

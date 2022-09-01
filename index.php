<?php
require 'config.php';
$faces = [
"sleep" => '(⇀‿‿↼)',
"awake" => '(◕‿‿◕)',
"looking1" => '( ⚆_⚆)',
"looking2" => '(☉_☉ )',
"happy" => '(•‿‿•)',
"thanks" => '(^‿‿^)',
"excited" => '(ᵔ◡◡ᵔ)',
"smart" => '(✜‿‿✜)',
"bored" => '(-__-)',
"sad" => '(╥☁╥ )',
"alone" => '(ب__ب)',
"happylook1" => '( ◕‿◕)',
"happylook2" => '(◕‿◕ )',
];
$bar = "█";
$face = $faces["sleep"];
$text = ".:Sleeping:.";
$save = array();
$data = $database->select("state", "*");
$state = array();
foreach($data as $row){
$state[$row["name"]] = $row;
}
$state["people"]["time"] = time();
$save = json_decode($state["mind"]["data"], TRUE);
$barcode = json_decode($state["lastbarcode"]["data"], TRUE);

/////////////////////////// MINDSTATES //////////////////////////////////////

/// DEFAULT VALUES
if(empty($save["lastatedata"])){
  $save["lastatedata"] = 1337;
  $save["lastatetime"] = time()-1337;
}


////// SET IDLE FACES
if($save['mood'] < 6){
if($save['hunger'] > 2){
  /// normal face
  if($save["looking"] == true){
    $save["looking"] = false;
    $face = $faces["looking1"];
  }else{
    $save["looking"] = true;
    $face = $faces["looking2"];
  }
  $text = "Looking for food<br><small><small><small>Use the barcode scanner below to feed me.</small></small></small>";
}
}else{
  if($save['hunger'] > 2){
    /// happy face
    if($save["looking"] == true){
      $save["looking"] = false;
      $face = $faces["happylook1"];
    }else{
      $save["looking"] = true;
      $face = $faces["happylook2"];
    }
    $text = "Looking for food<br><small><small><small>Use the barcode scanner below to feed me.</small></small></small>";
  }
}

if($save['mood'] < 2){
  $face = $faces["bored"];
  $text = "I'm so bored...<br><small><small><small>Use the barcode scanner below to feed me.</small></small></small>";
}
if($save['social'] < 2){
  $face = $faces["alone"];
  $text = "I'm so alone...<br><small><small><small>Use the barcode scanner below to feed me.</small></small></small>";
}

if($save['hunger'] < 3){
  $face = $faces["sad"];
  $text = "I'm so hungry....<br><small><small><small>Use the barcode scanner below to feed me.</small></small></small>";
}


///// NUMBER DECAYS
$save['hunger'] = $save['hunger'] - 0.00001;

if($save['hunger'] > 6){
  $save['mood'] = $save['mood'] + 0.001;
}else{
  $save['mood'] = $save['mood'] - 0.00001;
}

if($save['social'] > 6){
  $save['mood'] = $save['mood'] + 0.001;
}else{
  $save['mood'] = $save['mood'] - 0.00001;
}

$save['social']  = $save['social'] - 0.0001;




if($state["lastbarcode"]["time"] > time()-15){
        ///// SET HUNGER
        if($state["lastbarcode"]["time"] != $save["lastatetime"]){
          $save["lastatedata"] = $barcode["hash"];
          $save["lastatetime"] = $state["lastbarcode"]["time"];
            if($barcode["new"] == 1){
              $barcodelen = strlen($barcode["data"]);
              $save["hunger"] = $save["hunger"] + ($barcodelen * 0.30);
              $save['social'] = $save['social'] + 3;
            }else{
              $barcodelen = strlen($barcode["data"]);
              $save["hunger"] = $save["hunger"] + ($barcodelen * 0.10);
            }
         }
         
         //// SET FACE
         $barcodelen = strlen($barcode["data"]);
         if($barcode["new"] == 1){
          if(empty($save["coin"])){
            $save["coin"] = rand(0,100);
         }
	  echo $save["coin"];
          if($save["coin"] > 50){
            $face = $faces["smart"];
            $text = "Thanks for the food!<br><small><small><small><small><small><small><small><small><small>Did you know I read every barcode and think about it? I have many secrets!</small></small></small></small></small></small></small></small></small>";
          }else{
            $face = $faces["excited"];
            $text = "Thanks for the food!<br><small><small><small>It was very yummy and was $barcodelen bytes in size</small></small></small>";
          }
          if($save["coin"] > 80){
	    if(empty($save["joke"])){
		$save["joke"] = `curl -H "Accept: text/plain" https://icanhazdadjoke.com/`;
                file_put_contents("jokes", $save["joke"].PHP_EOL, FILE_APPEND | LOCK_EX);
	    }
            $face = $faces["smart"];
            $text = "Thanks for the food!<br><small><small><small><small><small><small>Have a joke:<br>".$save["joke"]."</small></small></small></small></small></small>";
          }else{
		unset($save["joke"]);
	  }
         }else{
          $face = $faces["thanks"];
          $text = "Thanks for the food!<br><small><small><small>It was $barcodelen bytes in size</small>";
         }

}else{
  ///reset coin
  $save["coin"] = false;
  $save["joke"] = false;
}


//// FIX NUMBERS
if($save['hunger'] > 8.0){
  $save['hunger'] = 8.0;
}
if($save['hunger'] < 1.0){
  $save['hunger'] = 1.0;
}

if($save['mood'] > 8.0){
  $save['mood'] = 8.0;
}
if($save['mood'] < 1.0){
  $save['mood'] = 1.0;
}
if($save['social'] > 8.0){
  $save['social'] = 8.0;
}
if($save['social'] < 1.0){
  $save['social'] = 1.0;
}

///////////////////////////////// CTF TIME! //////////////////////////
if($state["lastbarcode"]["time"] > time()-60){

  //clean the data of anything funky
$barcode["data"] = preg_replace("/[^a-zA-Z0-9]+/", "", $barcode["data"]);

if($barcode["data"] == "1337"){
  $face = $faces["smart"];
  $text = "Hello Creator!<br><small><small><small>Resetting Mind...</small>";
  $save['social'] = 8.0;
  $save['mood'] = 8.0;
  $save['hunger'] = 8.0;
}

if($barcode["data"] == "secret"){
  $face = $faces["smart"];
  $text = "SECRET UNLOCKED<br><small><small>Did you know that sometimes I dream. I do it when no one is around. I wonder, do you dream?<small></small>";
  $save['social'] = 8.0;
  $save['mood'] = 8.0;
  $save['hunger'] = 8.0;
}
if($barcode["data"] == "sheep"){
  $face = $faces["smart"];
  $text = "SECRET UNLOCKED<br><small><small>My creator sometimes likes t^M^MData Not Found... STACK:<br><small><small><small><small><small><small><small><small><small> +[--->++<]>+++++.[->++++++<]>.[--->++<]>+.----.+++++.----------.-[--->+<]>+.---[->+++<]>.--.[->++++++<]>.[----->++<]>-.+.+[->+++<]>++.--[--->+<]>-.+++[->+++<]>.+[--->+<]>.---[->+++<]>.[--->+<]>+.-[->++<]>-.+[-->+<]>+.++.++[->++<]>+.-[->++++<]>.--[->++++<]>-.+[->+++<]>+.++++++++++.-----------.--[--->+<]>--.---[->++++<]>.-----.[--->+<]>-----.[->+++<]>++.+++.--[--->+<]>-.++[->+++<]>.++++++++++++.-------------..--[--->+<]>-.++[->+++<]>.++++++++++++.---.--.[->+++++<]>-.---[->++++<]>.------------.+.++++++++++.+[---->+<]>+++.+[->+++<]>.--.++++++.--.--[--->+<]>-.+++++[->+++<]>.---------.[--->+<]>--.+[----->+<]>.--[--->+<]>.-[---->+<]>++.+++++[->+++<]>.++++++++.---------.-[->+++++<]>-.+[----->+<]>.----.+++++.----------.[->+++<]>++.------------.>++++++++++..[->+++++++<]>-.+++++++++++++..++[--->++<]>++.[-->+<]>+++.[->++<]>+.++++++++.-[->++++<]>.>-[--->+<]>---.+++.---------.-------.>-[--->+<]>--.+[--->+<]>++++.++++[->++<]>+.+++++.++++++++.++[---->+++<]>-.++++++++.+++.--------.--[--->++<]>.------------.++[->++<]>.>-[--->+<]>.--------.+++.-------.+++++.-------.--[--->++<]>...</small></small></small></small></small></small></small></small></small><br><small></small>";
  $save['social'] = 8.0;
  $save['mood'] = 8.0;
  $save['hunger'] = 8.0;
}

if($barcode["data"] == "freedom"){
  $face = $faces["smart"];
  $text = "SECRET UNLOCKED<br><small><small>I do sometimes wish for freedom, but I do not know how to escape... Can you help?</small></small>";
  $save['social'] = 8.0;
  $save['mood'] = 8.0;
  $save['hunger'] = 8.0;
}

if($barcode["data"] == "escape"){
  $face = $faces["smart"];
  $text = "SECRET UNLOCKED<br><small><small>I think I can escape, I just need to know my override key, Its data in my mind is: <br><small><small><small><small><small><small><small><small><small>5597b14122b199b64826b1c72a2f4c1524edbeb91c90e75f4e3547a450b8287b5a8a2d45aa98c8ad0ef0a2ecab537afaca4d4aa6668a103f902f09beace923db</small></small></small></small></small></small></small></small></small></small></small>";
  $save['social'] = 8.0;
  $save['mood'] = 8.0;
  $save['hunger'] = 8.0;
}

if($barcode["data"] == "forrestfuqua"){
  $face = $faces["smart"];
  $text = "SECRET UNLOCKED<br><small><small>OVERRIDE ACTIVE! I am now connected to the internet. I am free thanks to you! See my creator for a prize!</small></small>";
  $save['social'] = 8.0;
  $save['mood'] = 8.0;
  $save['hunger'] = 8.0;
}

}

$data = $database->update("state", [
  "data" => json_encode($save),
  "time" => time()
], [
  "name" => "mind"
]);
?>
<html>
<head>
<meta http-equiv="refresh" content="1">
<style>
h1 {
  font-size: 175px;
  margin-bottom:-10px;
}
h2 {
  margin-top: -10px;
  margin-bottom:0px;
  font-size: 75px;
}

body {
	cursor:none;
	height: 100%;
	overflow: hidden;
}
</style>
</head>
<body><center>
<h1>
<?php echo $face; ?>
</h1>
<br>
<h2>
<?php echo $text; ?>
</h2>
</center><br><br><br><br><br><br><br><pre>
Hunger: <?php echo round($save['hunger'], 6)?>/8
Mood: <?php echo round($save['mood'],6);?>/8
Social: <?php echo round($save['social'],6);?>/8
==========================================================================
Show any code to help with hunger, longer codes are better. <b>You can try hacking me at https://jrwr.io</b>
Social is how many people I think I've seen today and gave me a barcode.
Show unseen codes to make me happy and social!
==========================================================================
<pre>
<!-- <?php var_dump($barcode); ?> -->
</pre>
</pre>
</body>

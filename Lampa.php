<?php
    /*
	 * A LAMPA(http://esolangs.org/wiki/LAMPA) implement on php cli
	 * By Roman Didrag
	 */
    $stack = array();
	$p = 0;
	$l = array();	
	$b = -1;
	$source = file_get_contents($argv[1]);
    $source = preg_replace("/\-\-.+?\n/","",$source,-1);
	$source = explode(";",$source);
	
	function evl($source){
	global $stack,$p,$l,$b;
	for($i=0; $i<count($source); ++$i) {
		$ops = explode(" ",$source[$i]);
	    switch(strtolower(trim($ops[0]))){
			case "where":
			$l[trim($ops[1])] = $i;
			break;
			case "import":
			evl(file_get_contents(trim($ops[1])));
			break;
	    }
	}
	for($i=0; $i<count($source); ++$i) {
		$ops = explode(" ",$source[$i]);
		
		switch(strtolower(trim($ops[0]))){
			case "where":break;
			case "import":break;
			case "--":break;
			case "rec":
			if($b != -1){
				if(trim($ops[1]) == "proc"){
					$i = $b;
				}
			}
			break;
			case "hiding":
			if(trim($ops[1]) != "-1"){
				die(trim($ops[1]));
			} else {
				die();
			}
			break;
			case "as":
			$b = $i;
			$i = $l[trim($ops[1])];
			break;
			case "if":
			if(trim($ops[1]) != "in"){
			if((int) $stack[trim($ops[1])]){
				$i = $l[trim($ops[2])];
			}
			} else {
			if((int) $stack[trim($ops[2])] == (int) $stack[trim($ops[3])]){
				$i = $l[trim($ops[4])];
			}	
			}
			break;
			case "then":
			if(!(int) $stack[trim($ops[1])]){
				$i = $l[trim($ops[2])];
			}
			break;
			case "type":
			if(trim($ops[1]) != "as"){
			echo $stack[trim($ops[1])];
			} else {
			echo chr($stack[trim($ops[2])]);
			}
			break;
			case "let":
			if(trim($ops[2]) != "$"){
			$stack[trim($ops[1])] = (int) trim($ops[2]);
			} else if(trim($ops[2]) == "->") {
			$stack[trim($ops[1])] = $stack[trim($ops[3])];
			} else if(trim($ops[2]) == "#") {
			$stack[trim($ops[1])] = ord(fgets(STDIN));
			} else {
			$stack[trim($ops[1])] = (int) fgets(STDIN);
			}
			break;
			case "do:":
			if(trim($ops[1]) == "inc"){
				$stack[trim($ops[2])]++;
			} else if(trim($ops[1]) == "dec"){
				$stack[trim($ops[2])]--;
			} else if(trim($ops[1]) == "add"){
				$stack[trim($ops[2])]+=$stack[trim($ops[3])];
			} else if(trim($ops[1]) == "sub"){
				$stack[trim($ops[2])]-=$stack[trim($ops[3])];
			} else if(trim($ops[1]) == "set"){
				$stack[trim($ops[2])]=(int) trim($ops[3]);
			} else if(trim($ops[1]) == "move"){
				$stack[trim($ops[2])]=$stack[trim($ops[3])];
			} else if(trim($ops[1]) == "mul"){
				$stack[trim($ops[2])]*=$stack[trim($ops[3])];
			} else if(trim($ops[1]) == "div"){
				$stack[trim($ops[2])]/=$stack[trim($ops[3])];
			} else if(trim($ops[1]) == "mod"){
				$stack[trim($ops[2])]%=$stack[trim($ops[3])];
			}
			break;
			
			case "":break;
			case " ":break;
			case "\n":break;
			case "\t":break;
			default:
			die("\nLine ".$i.": Unknown command '".trim($ops[0])."'\n");break;
		}
		//echo "\nLINE: ".$i."\n";
	}
	}
	
	evl($source);
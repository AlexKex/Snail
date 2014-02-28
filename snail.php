<?php

class Snail {
	public $length = 5;
	public $answer = array();
	
	private $errors = array(
		0 => "Do you want to trick Gary?",
		1 => "That's wrong number!",
		2 => "Meow! Error... (",
		3 => "Did you forget numbers?",
		4 => "Gary don't like wrong numbers!",
		"big_num" => "That's too big for my hosting-provider... :( "
	);
	private $error = true;
	private $snail = array();
	private $finish;
	private $step = 1;
	private $move = 0;
	private $delta = 1;
	private $edge = 0;

	private $direction = 0; // 0 : x+, 1 : y-, 2 : x-, 3 : y+ 
	
	public function __construct($length){
		if((int)$length > 0){
			if($length > 550){
				$this->answer['ok'] = 0;
				$this->answer['errr'][] = $this->errors["big_num"];
			}
			else{
				$this->length = (int)$length;
				
				$this->finish = $this->length * 2; // final step
				
				for($i = 0; $i < $this->length; $i++){
					for($j = 0; $j < $this->length; $j++){
						$this->snail[$i][$j] = -1;
					}
				}
				$this->answer['ok'] = 1;
			}
		}	
		else{
			$this->answer['ok'] = 0;
			$err_key = rand(0, (count($this->errors) - 1));
			$this->answer['errr'][] = $this->errors[$err_key];
		}	
	}
	
	public function getError(){
		$this->answer['ok'] = 0;
		$err_key = rand(0, (count($this->errors) - 1));
		foreach($this->answer['errr'] as $e){
			$this->answer['error'] = $e.'
			';
		}
		unset($this->answer['errr']);
		
		return json_encode($this->answer);
	}
	
	public function cookSnail(){
		while($this->step < $this->finish ){	
			// calculating corner element
			$this->edge = $this->length * $this->step - $this->move;
			
			// fill empty cells
			$this->fillCells();
			
			// increase step
			$this->step++;
			$this->move = $this->move + $this->delta;

			// increase delta for NEXT step
			if($this->step % 2 != 0){
				$this->delta = ceil($this->step / 2);
			}
			
			$this->direction++;
			($this->direction > 3) ? $this->direction = 0 : '';
		}

        $this->answer['code'] = $this->drawSnail();
		$this->answer['ok'] = 1;
		return json_encode($this->answer);
	}

    private function drawSnail(){
        $table = "";
		$table .= "<table id='snail' border='1'>";
        foreach($this->snail as $key => $value){
            $table .=  "<tr>";
            foreach($value as $k => $v){
                $table .=  "<td>".$v."</td>";
            }
            $table .=  "</tr>";
        }
        $table .=  "</table>";
		
		return $table;
    }

	private function fillCells(){
		$vector_length = 0; // length of current line
		$coords = array('x' => 0, 'y' => 0); // start coords
		
		if($this->step > 1){
			$vector_length = $this->length - (floor($this->step / 2) - 1);
        }
		else
			$vector_length = $this->length;
			
		$coords = $this->getStartCoords();

        $this->drawVector($coords, $vector_length);
	}
	
	private function drawVector($coords, $length){
        $start = $this->edge - $length + 1;
		$el = ($this->step == 1) ? $start : $start+1;
		
		$x = $coords['x'];
		$y = $coords['y'];
		
		switch($this->direction){
            case 0:
				// fixed y; increase x;
				while($el <= $this->edge){
					if($this->snail[$y][$x] == -1){
						$this->snail[$y][$x] = $el;
					}
					$x++;
					$el++;
				}
                break;
            case 1:
                // fixed x; increase y;
                while($el <= $this->edge){
					if($this->snail[$y][$x] == -1){
						$this->snail[$y][$x] = $el;
					}
					$y++;
					$el++;
				}
                break;
            case 2:
                // fixed y; decrease x;
                while($el <= $this->edge){
					if($this->snail[$y][$x] == -1){
						$this->snail[$y][$x] = $el;
					}
					$x--;
					$el++;
				}
                break;
            case 3:
                // fixed x; decrease y;
                while($el <= $this->edge){
					if($this->snail[$y][$x] == -1){
						$this->snail[$y][$x] = $el;
					}
					$y--;
					$el++;
				}
                break;
        }
    }
	
	private function getStartCoords(){
		$coords = array('x' => 0, 'y' => 0);
		
		switch($this->direction){
			case 0: 
				// min x; min y
				foreach($this->snail as $key => $row){
					if(in_array(-1, $row)){
						$coords['y'] = $key;
						foreach($row as $k => $value){
							if($value == -1){
								$coords['x'] = $k;
								break;
							}
						}
						break;
					}
				}
			break;
			case 1: 
				// max x; min y
				foreach($this->snail as $key => $row){
					if(in_array(-1, $row)){
						$coords['y'] = $key;
						$row = array_reverse($row, true);
						foreach($row as $k => $value){
							if($value == -1){
								$coords['x'] = $k;
								break;
							}
						}
						break;
					}
				}
			break;
			case 2: 
				// max x; max y
				$sn = array_reverse($this->snail, true);
				foreach($sn as $key => $row){
					if(in_array(-1, $row)){
						$coords['y'] = $key;
						$row = array_reverse($row, true);
                        foreach($row as $k => $value){
							if($value == -1){
								$coords['x'] = $k;
								break;
							}
						}
						break;
					}
				}
			break;
			case 3: 
				// min x; max y
				$sn = array_reverse($this->snail, true);
				foreach($sn as $key => $row){
					if(in_array(-1, $row)){
						$coords['y'] = $key;
						foreach($row as $k => $value){
							if($value == -1){
								$coords['x'] = $k;
								break;
							}
						}
						break;
					}
				}
			break;
		}
		
		return $coords;
	}
}

$length = (int)$_POST['length'];
$turbo = new Snail($length);
if(isset($turbo->answer['ok']) && $turbo->answer['ok'] != 0){
	echo $turbo->cookSnail();
}
else
	echo $turbo->getError();

//$turbo->test();

<?php

/**
* Classe per gestire le varie funzioni che creano un codice fiscale
*
* @author     cecilia natale <info@cecilianatale.it>
* @version    1.0
* ...
*/

class codicefiscale {

	const ERR_GENERIC	= 'Errore di calcolo del codice fiscale.';
	const ERR_INVALID_URL	= 'Invalid ulr ';
	
	//Array delle consonanti
	protected $_consonanti = array(
		'B', 'C', 'D', 'F', 'G', 'H', 'J', 'K',
		'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T',
		'V', 'W', 'X', 'Y', 'Z'
	);
	
	//Array delle vocali
	protected $_vocali = array(
		'A', 'E', 'I', 'O', 'U'
	);
	
	//Array dei mesi
	protected $_mesi = array(
		'01' => 'A',	'02' => 'B',	'03' => 'C',
		'04' => 'D',	'05' => 'E',	'06' => 'H',
		'07' => 'L',	'08' => 'M',	'09' => 'P',
		'10' => 'R',	'11' => 'S',	'12' => 'T'
	);
	
	//array dei pari
	protected $_pari = array(
		'0' => 0,	'1' => 1,	'2' => 2,	'3' => 3,	'4' => 4,	
		'5' => 5,	'6' => 6,	'7' => 7,	'8' => 8,	'9' => 9,
		'A'	=> 0,	'B' => 1,	'C' => 2,	'D' => 3,	'E' => 4,
		'F' => 5,	'G' => 6,	'H' => 7,	'I' => 8,	'J' => 9,
		'K' => 10,	'L' => 11,	'M' => 12,	'N' => 13,	'O' => 14,
		'P' => 15,	'Q' => 16,	'R' => 17,	'S' => 18,	'T' => 19,
		'U' => 20,	'V' => 21,	'W' => 22,	'X' => 23,	'Y' => 24,
		'Z' => 25
	);
	
	
	//Array dei dispari
	protected $_dispari = array(
		'0' => 1,	'1' => 0,	'2' => 5,	'3' => 7,	'4' => 9,	
		'5' => 13,	'6' => 15,	'7' => 17,	'8' => 19,	'9' => 21,
		'A' => 1,	'B' => 0,	'C' => 5,	'D' => 7,	'E' => 9,
		'F' => 13,	'G' => 15,	'H' => 17,	'I' => 19,	'J' => 21,
		'K' => 2,	'L' => 4,	'M' => 18,	'N' => 20,	'O' => 11,
		'P' => 3,	'Q' => 6,	'R' => 8,	'S' => 12,	'T' => 14,
		'U' => 16,	'V' => 10,	'W' => 22,	'X' => 25,	'Y' => 24,
		'Z' => 23
	);
	
	
	protected $_check_digit = array(
		'0'		=> 'A',		'5' => 'F',		'10' => 'K',	'15' => 'P',	'20' => 'U',
		'1'		=> 'B',		'6' => 'G',		'11' => 'L',	'16' => 'Q',	'21' => 'V',
		'2'		=> 'C',		'7' => 'H',		'12' => 'M',	'17' => 'R',	'22' => 'W',
		'3'		=> 'D',		'8' => 'I',		'13' => 'N',	'18' => 'S',	'23' => 'X',
		'4'		=> 'E',		'9' => 'J',		'14' => 'O',	'19' => 'T',	'24' => 'Y',
 	 	'25'	=> 'Z'
	);
	

	/**
	 * Ritorna il codice fiscale utilizzando 
	 * i parametri passati a funzione
	 * @param type $cognome
	 * @param type $nome
	 * @param type $data
	 * @param type $sesso
	 * @param type $comune
	 */
	public function calcola($cognome, $nome, $data, $sesso, $comune) {
		
		$cf = '';
		$cf =	$this->calcolaCognome(strtoupper($cognome)) .
				$this->calcolaNome(strtoupper($nome)) .
				$this->calcolaData($data,strtoupper($sesso)) .
				$this->calcolaComune($comune);
		
		$cf = strtoupper($cf);
		
		$cf .= $this->calcolaCodiceControllo($cf);
		
		if(strlen($cf) != 16){
			 $this->_setError(self::ERR_GENERIC);
			 return false;
		}
		echo "codice fiscale: " . $cf;
		return $cf;
	}

	/**
	 * 
	 * @param type $string
	 */
	protected function calcolaCognome($string) {
		
		//se sono meno di 3 lettere aggiungo delle x
		if(strlen($string) < 3)
			return $this->addMissingLetter($cognome);

		$consonanti = $this->getLetter($string, $this->_consonanti);
		
		//prendo le prime 3 consonanti
		for ($i=0; $i<3; $i++) {
            if (array_key_exists($i, $consonanti)) {
                $cognome .= $consonanti[$i];
            }
        }
		
		//se non bastano prendo le vocali 
		if (strlen($cognome) < 3) {
            $vocali = $this->getLetter($string, $this->_vocali);
            while (strlen($cognome) < 3) {
                $cognome .= array_shift($vocali);
            }
        }
		
		return $cognome;
	}
	
	/**
	 * 
	 * @param type $string
	 */
	protected function calcolaNome($string) {				
		// se sono meno di 3 lettere aggiungo delle x
        if (strlen($string) < 3) {
            return $this->_addMissingX($nome);
        } 
		
		$consonanti = $this->getLetter($string, $this->_consonanti);
		
		// Se sono minori o uguali a 3 
		// vengono considerate nell'ordine in cui compaiono
        if (count($consonanti) <= 3) {
            $nome = implode('', $consonanti);
        } else {
            // Se invece abbiamo almeno 4 consonanti, prendiamo
            // la prima, la terza e la quarta.
            for($i=0; $i<4; $i++) {
                if ($i == 1) continue;
                if (!empty($consonanti[$i])) {
                    $nome .= $consonanti[$i];
                }
            }
        }
		
		// Se compaiono meno di 3 consonanti si utilizzano le vocali
		// nell'ordine in cui compaiono nel nome.
        if (strlen($nome) < 3) {
            $vocali = $this->getLetter($string, $this->_vocali);
            while (strlen($nome) < 3) {
               $nome .= array_shift($vocali); 
            }
        }
		
		return $nome;
	}
	
	/**
	 * 
	 * @param type $string
	 */
	protected function calcolaData($data, $sesso) {
		
		$elementi = explode('/', $data);
		$giorno = $elementi[0];
		$mese = $elementi[1];
		$anno = $elementi[2];
		
		$aa = substr($anno, -2);//codice anno		
		if(strlen($mese) < 2) $mese = '0' . $mese; 
		$mm = $this->_mesi[$mese];//codice mese
		$gg = ($sesso == 'F') ? ($giorno + 40) : $giorno;  //codice giorno
					
		if(strlen($gg) < 2) $gg = '0' . $gg; 
		
		$codice = $aa . $mm . $gg;
		return $codice;
	}

	/**
	 * 
	 * @param type $string
	 * @return type
	 */
	protected function calcolaComune($string){
		$url = "http://webservices.dotnethell.it/codicefiscale.asmx/CodiceComune";
		$param = 'NomeComune=' . $string;

		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $param);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec( $ch );
		curl_close($ch);

		if ($response === FALSE) {
			$error = curl_error($ch);
			$this->_setError($error);
			die($error);
		}
		
		$codice = simplexml_load_string($response);
		return $codice;
	}
	
	/**
	 * 
	 * @param type $string
	 * @return type
	 */
	protected function calcolaCodiceControllo($string) {
		$code = str_split($string);
		$sum = 0;
		
		for($i = 1; $i <= count($code); $i++){
			$cifra = $code[$i-1];
			$sum += ($i % 2) ? $this->_dispari[$cifra] : $this->_pari[$cifra];
		}
				
		$sum %= 26;
		$sum = $this->_check_digit[$sum];

		return $sum;
	}
	
	
	
	/**************************************************************************/
	/*							funzioni di supporto						  */
	/**************************************************************************/
	
	/**
	 * Estae le lettere in base al tipo passato
	 * @param type $string
	 * @param type $type
	 * @return type
	 */
	protected function getLetter($string, $type) {
		$letters = array();
		foreach (str_split($string) as $needle) {
			if (in_array($needle, $type)) {
				$letters[] = $needle;
			}
		}

		return $letters;
	}
	
	/**
     * Imposta il messaggio di errore
     */
    protected function _setError($string) {
        $this->_error = $string;
    }  
	
	/**
     * Verifica la presenza di un errore.
     * Ritorna TRUE se presente, FALSE altrimenti.
     */
    public function hasError() {
        return !is_null($this->_error);
    }   
    
    /**
     * Ritorna la stringa di errore
     */
    public function getError() {
        return $this->_error;
    }

}

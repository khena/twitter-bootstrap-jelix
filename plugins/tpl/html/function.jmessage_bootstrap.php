<?php
/**
* @package     jelix
* @subpackage  jtpl_plugin
* @author      Loic Mathaud, Bruno Perles, Guillaume Peres
* @copyright   2008 Loic Mathaud
* @copyright   2011 Bruno Perles 
* @copyright   2013 Guillaume Peres
* @link        http://www.jelix.org
* @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

/**
* Minimal bootstrap version 2.X 
* function plugin :  Display messages from jMessage WITH BOOTSTRAP
* The content before the first exclamation mark will be the "title". If no exclamation mark is present, no title (strong tag)
* The boolean $canclose (def true) defines if the close button must be displayed
* The string $type defines also the css class which will be used by bootstrap. See the source code for the correspondances.   
*/

function jtpl_function_html_jmessage_bootstrap($tpl, $type = '', $canclose = true) {
	
	/**
	 * Divise le message en deux parties, renvoie le corps du message, et le titre en valeur si il est présent
	 * @param string $sMsg Le message
	 * @param string $sTitle le titre trouvé
	 * @param string $sSep le séparateur entre le titre et le corps
	 * @param bool $bIncluded le séparateur est-il inclus dans le titre?
	 * @return string
	 */
	function getContent($sMsg,&$sTitle,$sSep="!",$bIncluded = true){
		$i = strpos($sMsg,$sSep);
		if($i === FALSE){
			return $sMsg;	
		}else{
			if($bIncluded){
				$sTitle = substr($sMsg, 0,$i+1);	
			}else{
				$sTitle = substr($sMsg, 0,$i);
			}			
			return substr($sMsg, $i+1);
		}
	}
	
	/**
	 * Retourne la classe CSS en fonction du type du message
	 * Si introuvable, info
	 * @param string $sType un des types suivants success - warning - info - error
	 * @return string
	 */
	function getCssClass($sType){		
		// CSS disponibles : success info warning danger		
		switch ($sType) {
			case 'ok':
			case 'success':
				return 'success';
				break;
			case 'warning':
				return 'warning';
				break;
			case 'danger':
			case 'error':
				return 'danger';
				break;
			case 'update':
			case 'info':
			default:
				return 'info';
				break;
		}
				
	}
	
	/**
	 * Echo du message
	 * @param string $sType le type de message 
	 * @param string $sMsg le message brut avant traitement
	 * @param bool $bCanClose le message peut-il être fermé?
	 * @return void
	 */
	function echoMsg($sType,$sMsg,$bCanClose){
	    /*
			<div class="alert alert-success">
				<button class="close" data-dismiss="alert">×</button>
				<strong>Success!</strong> The page has been added.
			</div>
		 */		
		$title = "";
        $content = getContent($sMsg,$title);
        echo '<div class="alert alert-'.getCssClass($sType).'">';
		if($bCanClose) echo '<button class="close" data-dismiss="alert">×</button>';
		if(!empty($title)){
			echo '<strong>'.htmlspecialchars($title).'</strong>';
		}
		echo htmlspecialchars($content);	       
		echo '</div>';
	}
		
    // Get messages
    if ($type == '') {
        $messages = jMessage::getAll();
    } else {
        $messages = jMessage::get($type);
    }
    // Not messages, quit
    if (!$messages) {
        return;
    }

    // Display messages
    
    if ($type == '') {        
        foreach ($messages as $type_msg => $all_msg) {
            foreach ($all_msg as $msg) {
				echoMsg($type_msg,$msg,$canclose);
            }
        }
    } else {
        foreach ($messages as $msg) {
                echoMsg($type,$msg,$canclose);
        }
    }    

    if ($type == '') {
        jMessage::clearAll();
    } else {
        jMessage::clear($type);
    }
    
}

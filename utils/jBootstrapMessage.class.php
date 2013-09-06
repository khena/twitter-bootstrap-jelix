<?php
/**
* @package    jelix
* @subpackage utils
* @author     Guillaume Peres
* @copyright  2013 Guillaume Peres
* @link       http://www.jelix.org
* @licence    GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

/**
 * Structural class to register all properties for a message
 * @package jelix
 * @subpackage utils
 */
class jStructuralBootstrapMessage {
	
	/**
	 * The type of the message
	 * @var string
	 */
	protected $_sType;
	
	/**
	 * The title of the message
	 * @var string
	 */
	public $title;
	
	/**
	 * The content
	 * @var string
	 */
	public $content;
	 
	/**
	 * The CSS class from bootstrap
	 * @var string
	 */
	protected $_sCssClass;

	function __construct($message, $type = 'default', $title = "", $cssclass = ""){
		$this->content = $message;
        $this->title = $title;
        $this->_sType = $type;
        $this->_sCssClass = $cssclass;
	}
	
	/**
	 * Return the type of the message
	 * @return string
	 */
	public function getType(){
		return $this->_sType;
	}
	 
	/**
	 * Return the ccs class the message. Try to use the type to autodetect the css class
	 * @return string
	 */
	public function getCssClass(){
		if(empty($this->_sCssClass)){
            // CSS disponibles : success info warning danger
            switch ($this->_sType) {
                case 'ok' :
                case 'success' :
                    return 'alert-success';                    
                case 'warning' :
                    return 'alert-warning';                    
                case 'danger' :
                case 'error' :
                    return 'alert-danger';                    
                case 'update' :
                case 'info' :
                default :
                    return 'alert-info';                    
            }            
			return $this->getType();
		}else{
			return $this->_sCssClass;	
		}		
	}
	 
}


/**
* Utility class to log some message in session in order to be displayed in a bootstrap template
* @package    jelix
* @subpackage utils
* @static
*/
class jBootstrapMessage extends jMessage {

    /**
    * Add a message
    * @param string $message the message
    * @param string $type the message type ('default' by default)
	* @param string $title the title of the message (blank by default) 
	* @param string $cssclass in order to work with the tpl plugin, the bootstrap alert- class. If blank, $type is used. (blank by default)
	* @return void 
    */
    public static function add($message, $type = 'default', $title = "", $cssclass = "") {
		$_SESSION[self::$session_name][$type][] = new jStructuralBootstrapMessage($message,$type,$title,$cssclass);
    }

	/**
	 * One simple function for browsing the $_SESSION array
	 * @param string $sType the message type, if blanck, take all
	 * @param bool $bOnlyContent return only the content (like jMessage) instead of the structure
	 * @return mixed string[] jStructuralBootstrapMessage[]
	 */
	private static function internalGet($sType = "", $bOnlyContent = false){
        if (isset($_SESSION[self::$session_name])) {
        	$aRes = array();

			if(empty($sType)){
				$aBrowse = $_SESSION[self::$session_name]; 
			}elseif(isset($_SESSION[self::$session_name][$type])){
				$aBrowse = array($type => $_SESSION[self::$session_name][$type] ) ;	
			}else{
				return null;
			}
                        
            foreach($aBrowse as $sType => $aMsg){
                foreach($aMsg as $oMsg){
                	if($bOnlyContent){
                		$aRes[] = $oMsg->content;	
                	}else{
                		$aRes[] = $oMsg;
                	}
                }
            }            
			return $aRes;
        }
		
		return null;
	}


    /**
    * Get only the content (string) of the messages for the given type
    * @param string $type the message type ('default' by default)
    * @return string[]
    */
    public static function get($type = 'default') {
		return self::internalGet($type,true);
    }
    
    /**
    * Get only the content (string) of all messages
    * @return string[] 
    */
    public static function getAll(){
    	return self::internalGet("",true);
    }
	
	/**
	 * Get only the full structure of the messages for the given type
	 * @param string $type the message type ('default' by default)
	 * @return jStructuralBootstrapMessage[]
	 */
	public static function getFull($type = 'default'){
		return self::internalGet($type,false);
	}
	
	/**
	 * Get only the full structure of all messages
	 * @return jStructuralBootstrapMessage[]
	 */
	public static function getAllFull(){
		return self::internalGet("",false);
	}	
}

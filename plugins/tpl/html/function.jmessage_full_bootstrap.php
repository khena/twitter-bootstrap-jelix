<?php
/**
 * @package     jelix
 * @subpackage  jtpl_plugin
 * @author      gperes
 * @copyright   2013 Guillaume Peres
 * @link        http://www.teikhos.eu
 * @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
 */

/**
 * function plugin :  Display messages from jMessage WITH BOOTSTRAP
 */

function jtpl_function_html_jmessage_full_bootstrap($tpl, $type = '', $canclose = true) {

    /**
     * Echo du message
     * @param jStructuralBootstrapMessage $oMsg le message à afficher
     * @param bool $bCanClose le message peut-il être fermé?
     * @return void
     */
    function echoMsg($oMsg, $bCanClose) {
        /*
         <div class="alert alert-success">
         <button class="close" data-dismiss="alert">×</button>
         <strong>Success!</strong> The page has been added.
         </div>
         */
        echo '<div class="alert ' . $oMsg->getCssClass() . '">';
        if ($bCanClose)
            echo '<button class="close" data-dismiss="alert">×</button>';
        if (!empty($oMsg->title)) {
            echo '<strong>' . htmlspecialchars($oMsg->title) . '</strong>&nbsp;';
        }
        echo htmlspecialchars($oMsg->content);
        echo '</div>';
    }

    // Get messages
    if ($type == '') {
        $messages = jBootstrapMessage::getAllFull();
    } else {
        $messages = jBootstrapMessage::getFull($type);
    }
    // Not messages, quit
    if (!$messages) {
        return;
    }

    // Display messages
    foreach ($messages as $msg) {
        echoMsg($msg, $canclose);
    }

    if ($type == '') {
        jBootstrapMessage::clearAll();
    } else {
        jBootstrapMessage::clear($type);
    }

}

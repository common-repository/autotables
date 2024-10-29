<?php
/*
Plugin Name: Autotables
Plugin URI: http://www.hyper-world.de/en/computer-2/autotables/
Description: This plugin colorize table row in alternating colors automatically to improve readability.
Version: 1.0.1
Author: Jan Gosmann
Author URI: http://www.hyper-world.de
*/

//------------------------------------------------------------------------------
// Processes the content to colorize tables.
//------------------------------------------------------------------------------
function at_colorize( $content )
{
	$content = preg_replace_callback ("#(?is)<table>.*?</table>#",
			'at_process_table', $content);
	return $content;
}

//------------------------------------------------------------------------------
// Callback function for processing one single table.
//------------------------------------------------------------------------------
function at_process_table( $table )
{
	global $at_actclass;
	$at_actclass = get_option( 'at_row1' );

	return preg_replace_callback ("#(?is)<tr>(?!\s*<th)#", 'at_process_row',
			$table[0]);
}

//------------------------------------------------------------------------------
// Callback function for processing on single row.
//------------------------------------------------------------------------------
function at_process_row( $row )
{
	global $at_actclass;

	if( $at_actclass == get_option( 'at_row0' ) )
		$at_actclass = get_option( 'at_row1' );
	else
		$at_actclass = get_option( 'at_row0' );

  return '<tr class="' . $at_actclass . '">';
}

//------------------------------------------------------------------------------
// Installs the pathbar plugin.
//------------------------------------------------------------------------------
function at_install()
{
	add_option( 'at_row0', 'row0', 'This is the CSS class for odd table rows.' );
	add_option( 'at_row1', 'row1', 'This is the CSS class for even table rows.' );
}

//------------------------------------------------------------------------------
// Provides a configuration menu for this plugin.
//------------------------------------------------------------------------------
function at_confmenu()
{
  add_options_page( 'Autotables', 'Autotables', 'switch_themes', 'autotables.php',
			'at_confpage' );
}

//------------------------------------------------------------------------------
// Display the configuration page.
//------------------------------------------------------------------------------
function at_confpage()
{
	if( $_POST['at_update_options'] == 'Save' ) {
		update_option( 'at_row0', $_POST['at_row0'] );
		update_option( 'at_row1', $_POST['at_row1'] );
	}

	?>
		<div class="wrap" id="at_confpage">
			<h2>Autotables</h2>
			<form method="post">
				CSS class for odd rows: <input type="text" name="at_row0" value="<?php echo get_option( 'at_row0' ); ?>" /><br />
				CSS class for even rows: <input type="text" name="at_row1" value="<?php echo get_option( 'at_row1' ); ?>" /><br />
				<p class="submit">
					<input type="submit" name="at_update_options" value="Save"/>
				</p>
			</form>
		</div>
	<?php
}

//------------------------------------------------------------------------------
// Hooks
//------------------------------------------------------------------------------
add_action( 'activate_autotables.php' , 'at_install' );
add_action( 'admin_menu', 'at_confmenu' );
add_filter( 'the_content', 'at_colorize' );

?>

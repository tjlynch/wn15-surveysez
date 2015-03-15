<?php
/**
*index.php functionally survey_list.php
 *survey_list.php works with survey_view.php to create a list/view app.
 *
 * demo_list_pager.php along with demo_view_pager.php provides a sample web application
 *
 * The difference between demo_list.php and demo_list_pager.php is the reference to the 
 * Pager class which processes a mysqli SQL statement and spans records across multiple  
 * pages. 
 *
 * The associated view page, demo_view_pager.php is virtually identical to demo_view.php. 
 * The only difference is the pager version links to the list pager version to create a 
 * separate application from the original list/view. 
 * 
 * @package SurveySez
 * @author t.lynch <email here>
 * @version 1.0 2015/02/03
 * @link http://www.chromaff.com/
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License ("OSL") v. 3.0
 * @see survey_view.php
 * @see Pager_inc.php 
 * @todo Create survey_view.php page
 */

# '../' works for a sub-folder.  use './' for the root  
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials 
 
# SQL statement
$sql = 
	"
	select CONCAT(a.FirstName, ' ', a.LastName) AdminName, s.SurveyID, s.Title, s.Description, 
	date_format(s.DateAdded, '%W %D %M %Y %H:%i') 'DateAdded' from "
	. PREFIX . "surveys s, " . PREFIX . "Admin a where s.AdminID=a.AdminID order by s.DateAdded desc
	";
#Fills <title> tag. If left empty will default to $PageTitle in config_inc.php  
$config->titleTag = 'Surveys made in Seattle';

#Fills <meta> tags.  Currently we're adding to the existing meta tags in config_inc.php
$config->metaDescription = 'Pure PHP! ' . $config->metaDescription;
$config->metaKeywords = 'PHP,PHP,PHP,'. $config->metaKeywords;

# END CONFIG AREA ---------------------------------------------------------- 
if(isset($_REQUEST['act'])){$myAction = (trim($_REQUEST['act']));}else{$myAction = "";}

switch ($myAction) 
{//check 'act' for type of process
	/*case "add": //2) Form for adding new customer data
	 	addForm();
	 	break;
	case "insert": //3) Insert new customer data
		insertExecute();
		break; */
	default: //1)Show existing customers
	 	showTable();
}

function showTable()
{//Select Customer
	global $config;
	get_header();
	echo '<h3 align="center">' . smartTitle() . '</h3>';

	$sql = "select CONCAT(a.FirstName, ' ', a.LastName) AdminName, s.SurveyID, s.Title, s.Description, 
	date_format(s.DateAdded, '%W %D %M %Y %H:%i') 'DateAdded' from "
	. PREFIX . "surveys s, " . PREFIX . "Admin a where s.AdminID=a.AdminID order by s.DateAdded desc
	";
	$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
	if (mysqli_num_rows($result) > 0)//at least one record!
	
	{//show results
		echo '<table align="center" border="1" style="border-collapse:collapse" cellpadding="3" cellspacing="3">';
		echo '<tr>
				<th>SurveyID</th>
				<th>AdminName</th>				
				<th>Title</th>
				<th>Description</th>
				<th>DateAdded</th>
			</tr>
			';
		while ($row = mysqli_fetch_assoc($result))
		{//dbOut() function is a 'wrapper' designed to strip slashes, etc. of data leaving db
			echo '<tr>
					<td>'	
				     . (int)$row['SurveyID'] . '</td>
				    <td>' . dbOut($row['AdminName']) . '</td>
				    <td>' . dbOut($row['Title']) . '</td>
				    <td>' . dbOut($row['Description']) . '</td>
				    <td>' . dbOut($row['DateAdded']) . '</td>

				</tr>
				';
		}
		echo '</table>';
	}else{//no records
      echo '<div align="center"><h3>No Records.</h3></div>';
	}
	@mysqli_free_result($result); //free up resources
	
}

?>
<p></p>
<h3 align="center">Surveys</h3>

<p>This page, along with <b>demo_view_pager.php</b>, demonstrate a List/View web application.</p>
<p>It was built on the mysql shared web application page, <b>demo_shared.php</b></p>
<p>This page is the entry point of the application, meaning this page gets a link on your web site.  Since the current subject is muffins, we could name the link something clever like <a href="<?php echo VIRTUAL_PATH; ?>demo_list_pager.php">Muffins</a></p>
<p>Use <b>demo_list_pager.php</b> and <b>demo_view_pager.php</b> as a starting point for building your own List/View web application!</p> 
<?php
#reference images for pager
$prev = '<img src="' . VIRTUAL_PATH . 'images/arrow_prev.gif" border="0" />';
$next = '<img src="' . VIRTUAL_PATH . 'images/arrow_next.gif" border="0" />';

# Create instance of new 'pager' class
$myPager = new Pager(2,'',$prev,$next,'');
$sql = $myPager->loadSQL($sql);  #load SQL, add offset

# connection comes first in mysqli (improved) function
$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

if(mysqli_num_rows($result) > 0)
{#records exist - process
	if($myPager->showTotal()==1){$itemz = "survey";}else{$itemz = "surveys";}  //deal with plural
    echo '<div align="center"><b>We have ' . $myPager->showTotal() . ' ' . $itemz . '!</b></div>';
	while($row = mysqli_fetch_assoc($result))
	{# process each row
         echo '<div align="center">
         <a href="' . VIRTUAL_PATH . 'surveys/survey_view.php?id=' . (int)$row['SurveyID'] . '">' . dbOut($row['Title']) . '</a>';
         
         echo '</div>';
	}
	echo $myPager->showNAV(); # show paging nav, only if enough records	 
}else{#no records
    echo "<div align=center>What! No Survey?  There must be a mistake!!</div>";	
}
@mysqli_free_result($result);
get_footer();







<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<Title>Taski</Title>
<?php require_once('config.php') ?>
    <link href="css/stylesheet.css" rel="stylesheet" type="text/css" />
    <link href="css/jquery-ui-1.8.12.custom.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="js/jquery.js"></script>  
        <script type="text/javascript" src="js/javainclude.js"></script>
        <script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.12.custom.min.js"></script>
</head>
<body>
<?php
	if(!empty($_GET['updateTask']))
		SaveTask($_GET['updateTask']);
	
	if(!empty($_GET['deletetid']) )
		DeleteTask($_GET['deletetid']);
	
	if(!empty($_GET['CompID']) )
		CompleteTask($_GET['CompID']);
		
	if(!empty($_GET['EditTasklist']))
		UpdateTaskList($_POST["tlid"],$_POST["tlTitle"]);
			
	if(!empty($_GET['DeleteTaskList']))
		DeleteTaskList($_GET['DeleteTaskList']);
        
        if(!empty($_GET['upload']))
                UploadAttachment($_GET['upload']);
        
        if(!empty($_GET['DeleteAttachment']))
                DeleteAttachment($_GET['DeleteAttachment']);
?>

<div id="Content">
	<h1>Taski</h1>
	<h2>Tasks Tracking Made Easy</h2>
	
	<div id="tasklists">
	<?php
	$AllTaskLists = array();
	$taskCount;
	if ($db_found) {
		$SQL = "SELECT * FROM TaskLists";
		$result = mysql_query($SQL);
		
		echo "<div id='wrapper'>\n";
		while ($db_field = mysql_fetch_assoc($result)) {
                        echo "<div class='accordionButton'>" . $db_field['Title']  . "</div>\n";
                        echo "<div class='accordionContent'>\n<div class='column'>\n".Tasks($db_field['tlid'])."</div></div>\n";
                        if($db_field['tlid']!="0")
                            $AllTaskLists[$db_field['tlid']] = $db_field['Title'];
		}
		
		echo "<div class='accordionButton'>Settings</div>\n";
		echo "<div class='accordionContent'>\n";
		echo Settings()."</div>\n";
		
		echo "</div>\n";
	}
	else {
		echo "<h4>Database Not Found</h4>";
		echo mysql_error($db_found); 
		mysql_close($db_handle);
	}
	?>
	
	</div>
        
	<div id="newtask">
		<?php echo TaskInfo($_GET['tid']); ?>
	</div>
		
	<?php	
		function Tasks($TLID) {
		global $db_found;
		$result = "";
		if ($db_found) {
			$SQL = "SELECT * FROM Tasks WHERE TLID='".$TLID."'";
			$output = mysql_query($SQL);
		
			while ($db_field = mysql_fetch_assoc($output)) {
				if($db_field['Completed'])
				{
					$isCompleted = "Checked";
				}
				else
				{
					$isCompleted = "";
				}
				//Check for length of notes
				$strLength = 300;
				if(strlen($db_field['Notes']) > $strLength)
				{
					$Notes = substr($db_field['Notes'],0,$strLength) . "...";
				}
				else
				{
					$Notes = $db_field['Notes'];
				}
				
				$result .= "<div class='portlet'>\n";
				$result .=  "<div class='portlet-header' id='TID".$db_field['TID']."'>".$db_field['Title'];
				$result .= "<span style='float:right;'>Completed: <input type='checkbox' name='CompleteTask' value='".$db_field['TID']."' ".$isCompleted." class='taskcompleted' /> |<span class='tooglesize'>&nbsp;</span></span></div>";
				$result .=  "<div class='portlet-content' id='TID".$db_field['TID']."'>";
				$result .=  "<table>\n";
				$result .=  "<tr><td><b>Status: </b>".$db_field['Status']."</td>";
				$result .=  "<td><b>Created: </b>".$db_field['CreatedDate']."</td>";
				$result .=  "<td><b>Due Date: </b>".$db_field['DueDate']."</td></tr>\n";
				$result .=  "<tr><td colspan='3'><b>Notes: </b>".$Notes."</td></tr>\n";
				$result .=  "</table>\n";
				$result .=  "</div>";
				$result .=  "</div>";
			}
		}
		else {
			$result .= "<h3>Database Not Found</h3>\n";
			$result .= mysql_error($db_found); 
			mysql_close($db_handle);
		}
		
		return $result;
		}
		
		function TaskInfo($TaskID) {
		global $db_found;
		global $AllTaskLists;
			//Check for task info
			if (!empty($TaskID))
			{
				//Lookup DB Values for TaskID
				if ($db_found) {
				$SQL = "SELECT * FROM Tasks WHERE TID='".$TaskID."'";
				$output = mysql_query($SQL);
			
				while ($db_field = mysql_fetch_assoc($output)) {
						$Title = $db_field['Title'];
						$Notes=  $db_field['Notes'];
						$Status =  $db_field['Status'];
						$DueDate = $db_field['DueDate'];
						$CreatedDate = $db_field['CreatedDate'];
						$Completed = $db_field['Completed'];
						$CompletedDate = "Completed : ";
						$thisTLID = $db_field['TLID'];
						$ButtonEnable = "";
						if ($Completed)
						{
							$Completed = "Checked";
							$CompletedDate .= "<b>".$db_field['CompletedDate']."</b>";
						}
						else
						{
							$Completed = "";
						}
					}
				}
			}
			else
			{
				//Default Values
				$TaskID = "X";
				$CompletedDate = "Completed";
				$Title = "Title";
				$Notes =  "Notes";
				$Status = "Status";
				$CreatedDate = Date("Y-m-d");
				$DueDate = $CreatedDate;
				$ButtonEnable = "disabled='disabled'";
			}

			
			//Being Form
			$return .= "<table width='100%'>\n<form name='taskForm' action='".$_SERVER["PHP_SELF"]."?updateTask=".$TaskID."' method='POST'>";
			$return .= "<tr><td colspan='2'><input name='TaskTitle' class='input' type='text' id='TaskTitle' size='41' tabindex='1' value='".$Title."' style='width:  100%;' /></td></tr>\n";
			$return .= "<tr><td colspan='2'><textarea cols='32' class='textarea' rows='10' name='TaskNotes' id='TaskBody' tabindex='2' style='width:  100%;'>".$Notes."</textarea></td></tr>\n";
			$return .= "<tr><td>Created:</td><td><input name='TaskStatus' class='input' type='text' id='TaskStatus' size='23' tabindex='3'  value = '". $Status. "' style='width:100%;' /></td></tr>\n";
			
			$return .= "<tr><td><b>".$CreatedDate."</b></td><td><select name='TaskList' tabindex='4' style='width:100%;'>n";
			foreach($AllTaskLists as $key => $value)
			{
				if ($key == $thisTLID)
				{
					$return .= "<option value='".$key."' selected>".$value."\n";
				}
				else
				{
					$return .= "<option value='".$key."'>".$value."\n";
				}
			}
			$return .= "</select></td><td></tr>\n";
			$return .= "<tr><td>Due Date:</td><td><input type='checkbox' name='TaskCompleted' value='1' ".$Completed." tabindex='5' /><label for='Completed'>".$CompletedDate."</label></td></tr>\n";
			$return .= "<tr><td rowspan='2' style='vertical-align:top'><input name='TaskDueDate' class='input' type='text' id='TaskDueDate' size='10' tabindex='6' value='".$DueDate."'  /></td><td><input type='submit' value='Save' style='width:100%;height:30px;' tabindex='7' /></td></tr>\n";
			$return .= "<tr><td><input type='reset' value='Reset' style='width:50%;' tabindex='8' name='TaskReset' /><input type='button' value='Delete' style='width:50%;' name='TaskDelete' tabindex='9' id='DeleteButton' tid='$TaskID' $ButtonEnable /></td></tr>\n";
			$return .= "</table></form>\n";
			$return .= "<input type='button' value='New' style='width:100%;height:30px;' id='New' />\n";
			//Attachment Footer
			if ($TaskID!="X")
			{
				$return .= "<div id='attachmentheader'><h4>Attachment Manager</h4></div>\n";
				$return .= "<div id='attachments' style='display: none;'>\n<table>";
				$return .= GetAttachments($TaskID);
                                $return .= "<tr><td><form name='attachment' action='".$_SERVER["PHP_SELF"]."?upload=$TaskID&tid=$TaskID' method='POST' enctype='multipart/form-data'><input type='file' name='file' id='file' style='width:98%' /></td>\n";
                                $return .= "<td><input type='submit' value='Upload' style='width:100%;height:25px;' /></td></tr>\n";
				$return .= "</table></div>\n";
			}


		return $return;		
		}
	
		function DeleteTask($DeleteTaskID) {
			global $db_found;
			
			//Delete the task then output the banner
			$DeleteTaskID = mysql_real_escape_string($DeleteTaskID);
			
			if ($db_found) {
					$SQL = "DELETE FROM Tasks WHERE TID='$DeleteTaskID'";
					
					mysql_query($SQL) or die(mysql_error());
					mysql_close($db_handle);
					$result = "<div id='Success'>";
					$result .= "Task Deleted Successfully";
			}
			else
			{
					$result = "<div id='Error'>Unable to delete task";
			}
				
			$result .= "</div>";
			echo $result;
		}
		
		function CompleteTask($CompleteTaskID) {
			global $db_found;
			
			//Complete the task then output the banner
			$CompleteTaskID = mysql_real_escape_string($CompleteTaskID);
			
			if ($db_found) {
					$SQL = "UPDATE Tasks SET Completed='1', CompletedDate=CURDATE() WHERE TID='$CompleteTaskID'";
									
					mysql_query($SQL) or die(mysql_error());
					mysql_close($db_handle);
					$result = "<div id='Success'>";
					$result .= "Task Completed Successfully";
			}
			else
			{
					$result = "<div id='Error'>Unable to delete task";
			}
				
			$result .= "</div>";
			echo $result;
		}
		
		function SaveTask($UpdateTaskID) {
			//Save's Query then presents a banner along the top with the result :)
			global $db_found;
			$UpdateTaskID = mysql_real_escape_string($UpdateTaskID);
			
			//Form Values
			$Title = mysql_real_escape_string($_POST["TaskTitle"]);
			$Notes = mysql_real_escape_string($_POST["TaskNotes"]);
			$Status = mysql_real_escape_string($_POST["TaskStatus"]);
			$DueDate = mysql_real_escape_string($_POST["TaskDueDate"]);
			$TLID = mysql_real_escape_string($_POST["TaskList"]);
			$Completed = mysql_real_escape_string($_POST["TaskCompleted"]);
			if($Completed)
			{
				$CompletedDate = "CURDATE()";
			}
			
			//Check that the default value is not set
			if ($Title == "Title")
			{
				$result = "<div id='Error'>Task Not Saved</div>";
				echo $result;
				return;
			}

			
			//First if a new Task then the $TaskID will be X
			if($UpdateTaskID == "X")
			{
				if ($db_found) {
					$SQL = "INSERT INTO Tasks (Title,Notes,Status,DueDate,TLID,SourceDevice,CreatedDate) VALUES ('$Title','$Notes','$Status',FROM_UNIXTIME('$DueDate'),'$TLID','WebSite',CURDATE())";
					
					mysql_query($SQL) or die(mysql_error());
					mysql_close($db_handle);
					$result = "<div id='Success'>";
					$result .= "Task Saved Successfully";
				}
			}
			else
			{
			//Now update Task
				if ($db_found) {
					$SQL = "UPDATE Tasks SET Title='$Title', Notes='$Notes', Status='$Status', DueDate=FROM_UNIXTIME('$DueDate'), Completed='$Completed', CompletedDate='$CompletedDate', TLID='$TLID' WHERE TID=$UpdateTaskID";
					
					mysql_query($SQL) or die(mysql_error());
					mysql_close($db_handle);
					$result = "<div id='Success'>";
					$result .= "Task Updated Successfully";
				}
			}
			
			$result .= "</div>";
			echo $result;
		}
                
		function UpdateTaskList($TaskListID, $TaskListTitle) {
			   global $db_found;
		
		//Check if TLID is value is set or not
		$TaskListID = mysql_real_escape_string($TaskListID);
		$TaskListTitle = mysql_real_escape_string($TaskListTitle);
					
					//First Update existing
					if(!empty($TaskListID))
					{
						if ($db_found) {
							$SQL = "UPDATE TaskLists SET Title='$TaskListTitle' WHERE tlid='$TaskListID'";
							mysql_query($SQL) or die(mysql_error());
							mysql_close($db_handle);
							$result = "<div id='Success'>";
							$result .= "Tasklist Updated";
						}
						else
						{
							$result = "<div id='Error'>Unable to update task list";
						}
					}
					else
					{   
						//Create New
						if ($db_found) {
							$SQL = "INSERT INTO TaskLists (Title) VALUES('$TaskListTitle')";
							mysql_query($SQL) or die(mysql_error());
							mysql_close($db_handle);
							$result = "<div id='Success'>";
							$result .= "Tasklist Created";
						}
						else
						{
							$result = "<div id='Error'>Unable to create TaskList";
						}
					}
		$result .= "</div>";
		echo $result;
			}
			
		function DeleteTaskList($TaskListID) {
                    //Check if TLID is value is set or not
                    $TaskListID = mysql_real_escape_string($TaskListID);
                    global $db_found;
                    
                    //First Update existing
                    if(!empty($TaskListID))
                    {
                        //First delete tasklist
                        if ($db_found) {
                            $SQL = "DELETE FROM TaskLists WHERE tlid='$TaskListID'";
                            mysql_query($SQL) or die(mysql_error());

                            //Set replace the tasklist id in existing tasks
                            $SQL = "UPDATE Tasks SET TLID='0' WHERE TLID='$TaskListID'";
                            mysql_query($SQL) or die(mysql_error());

                            mysql_close($db_handle);
                            $result = "<div id='Success'>";
                            $result .= "Tasklist Updated";
                        }
                        else
                        {
                            $result = "<div id='Error'>Unable to update task list - $TaskListID";
                        }
                    }
                    $result .= "</div>";
                    echo $result;
                }
                
		function Settings(){
			global $AllTaskLists;
			$output = "<h2>Task Lists</h2>\n";
			$output .= "<table width='100%' border='0' padding='1' style='bgcolor:#616161;' >\n";
			foreach($AllTaskLists as $key => $value)
			{
                            $output .= "<tr bgcolor='white'><td width='70%' style='padding-left:10px'><b>";
                            $output .= $value ."</b></td><td><input type='Button' value='Edit' style='width:50%' tasktitle='$value' tlid='$key' class='EditTaskList' /><input type='Button' value='Delete' style='width:40%' tlid='$key' class='DeleteTaskList' /></td></tr>\n";
			}
                        $output .= "<tr><td colspan='2'><hr width='100%' align='center' /></td></tr>";
                        $output .= "<tr><td align='center'><form name='tasklistupdate' action='".$_SERVER["PHP_SELF"]."?EditTasklist=Update' method='POST'><input type='text' id='tlTitle' name='tlTitle' size='60' /><input type='hidden' id='tlid' name='tlid' value='' /></td><td><input type='submit' value='Save' style='width:70%' /></form></td></tr>";
			$output .= "</table>\n";
			echo $output;
		}
                
                function UploadAttachment($TaskID) {
                    error_reporting(0);
                    global $filepath;
                    //Check for valid tid
                    if(!empty($TaskID))
                    {   
                        //  //Check the file selection is valid
                        if ($_FILES["file"]["error"] > 0)
                        {
                            //Failure
                          $result = "Problem with upload: " . $_FILES["file"]["error"] . "</div>\n";
                        }
                        else
                        {
                          //success
                          $newpath = "files/" . $TaskID;
                          $res = mkdir($newpath,0777,true);
                          move_uploaded_file($_FILES["file"]["tmp_name"],$newpath . "/" . $_FILES["file"]["name"]);
                          $savedfileloc = $filepath . $TaskID . "/" . $_FILES["file"]["name"];
                          $result = "<div id='Success'>File saved to: <a href='$savedfileloc'>$savedfileloc</a> - ".$_FILES["file"]["name"]."</div>\n";
                          
                          //now save string
                          InsertAtachmentMySQL($TaskID,$_FILES["file"]["name"]);
                        }
                        
                    }
                    else
                    {
                        $result = "<div id='Error'>Unable to save attachment</div>\n";
                    }
                    
                    echo $result;
                }
                
                function InsertAtachmentMySQL($TaskID,$File) {
                    //clean input and get DB connection
                    global $db_found;
                    $TaskID = mysql_real_escape_string($TaskID);
                    $File = mysql_real_escape_string($File);
                    
                    if ($db_found) {
                        $SQL = "INSERT INTO attachments (tid,filename,source) VALUES('$TaskID','$File','website')";
                        mysql_query($SQL) or die(mysql_error());
		    }
                }
                
                function GetAttachments($TaskID){
                   //Gets a list of file attachments
                    //Standard location + TID (as folder) then file
                    $TaskID = mysql_real_escape_string($TaskID);
                    global $filepath;
                    global $db_found;
                    
                    if ($db_found) {
                            //Get the info from the server
                            $SQL = "SELECT * FROM attachments WHERE tid='".$TaskID."'";
                            $output = mysql_query($SQL);
		
                            while ($db_field = mysql_fetch_assoc($output)) {
				$tmppath = $filepath.$TaskID."/".$db_field['filename'];
                                $result .= "<tr><td><a href='$tmppath'>".$db_field['filename']."</a></td><td><input type='Button' value='Delete' style='width:100%;height:25px;' aid='".$db_field['aid']."' class='DeleteAttachment' /></td></tr>\n";
                            }
                    }
                    else
                    {
                        $result .= "<tr><td>Unable to connect to DB</td></tr>";
                        
                    }
                    
                    return $result;
                }
                
                function DeleteAttachment($AID) {
                    //Lookup info about the attachment then use it to remove the file and then remove the MySQL Record
                    $AID = mysql_real_escape_string($AID);
                    global $db_found;
                    
                    if ($db_found){
                        //First get the AID info
                        $SQL = "SELECT * FROM attachments WHERE aid='$AID'";
                        $output = mysql_query($SQL);
                        while ($db_field = mysql_fetch_assoc($output)) {
                            $FileName = $db_field['filename'];
                            $TID =  $db_field['tid'];
                        }
                        
                        //Now try to remove the file
                        unlink("files/".$TID."/".$FileName);
                        echo "files/".$TID."/".$FileName;
                        
                        //Now remove the attachment from the MySQL DB
                        $SQL = "DELETE FROM attachments WHERE aid='$AID'";
                        mysql_query($SQL) or die(mysql_error());
                        
                        $result = "<div id='Success'>";
			$result .= "Attachment deleted";
                    }
                    else
                    {
                        $result = "<div id='error'>Unable to delete attachment";
                    }
                    
                    $result .= "</div>";
                    echo $result;
                }
	?>	
    </div>
</body>
</html>
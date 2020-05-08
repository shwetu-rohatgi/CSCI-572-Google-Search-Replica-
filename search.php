<?php

// make sure browsers see this page as utf-8 encoded HTML
header('Content-Type: text/html; charset=utf-8');

$limit = 10;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$ranking = isset($_REQUEST['ranking']) ? $_REQUEST['ranking'] : "lucene";
$results = false;
//echo($query);

if ($query)
{ 
  // The Apache Solr Client library should be on the include path
  // which is usually most easily accomplished by placing in the
  // same directory as this script ( . or current directory is a default
  // php include path entry in the php.ini)
  include('Apache/Solr/Service.php');
  //echo("Working");

  // create a new solr service instance - host, port, and webapp
  // path (all defaults in this example)
  $solr = new Apache_Solr_Service('127.0.0.1', 8983, 'solr/solr_core');
  //echo("Working2!");

  // if magic quotes is enabled then stripslashes will be needed
  if (get_magic_quotes_gpc() == 1)
  {
    $query = stripslashes($query);
  }

  // in production code you'll always want to use a try /catch for any
  // possible exceptions emitted  by searching (i.e. connection
  // problems or a query parsing error)
  try
  {
    if ($ranking=="pagerank"){
	//echo "pageRank";
     	$additionalParameters = array(
    	"sort"=>"pageRankFile desc"
     	);
    	$results = $solr->search($query, 0, $limit, $additionalParameters);
     }
    else{
	//echo "lucene";
    	$results = $solr->search($query, 0, $limit, $additionalParameters);
     }
  }
  catch (Exception $e)
  {
    // in production you'd probably log or email this error to an admin
    // and then show a special message to the user but for this example
    // we're going to show the full exception
    die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
  }
}

?>
<html>
  <head>
    <title>PHP Solr Search Client</title>
  </head>
  <body>
   <center>
    <h2>PHP Solr Search Client</h2>
    <br>
    <form accept-charset="utf-8" method="get">
      <label for="q">Search:</label>
      <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>
      <br>
      Ranking Algorithm:
      <input type="radio" name="ranking" value="lucene">Lucene
      <input type="radio" name="ranking" value="pagerank">PageRank
      <br>
      <br>
      <input type="submit"/>
    </form>
<?php

// display results
if ($results)
{
  $total = (int) $results->response->numFound;
  $start = min(1, $total);
  $end = min($limit, $total);
?>
    <div>Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>
    <ol>
<?php
  // iterate result documents
  foreach ($results->response->docs as $doc)
  {	
	$ID = "N/A";
	$title = "N/A";
	$page_url = "N/A";
	$page_desc = "N/A";
?>
      <li>
        <table style="border: 1px solid black; text-align: left; width:1700px;">
<?php
    // iterate document fields / values
    foreach ($doc as $field => $value)
    {
	if($field=="id"){
		$ID = $value;
	}
	if($field=="title"){
		if(sizeof($value)>1){
		$title = $value[0];
		}
		else{
		$title = $value;
		}

	}
	if($field=="og_url"){
		if(sizeof($value)>1){
		$page_url = $value[0];
		}
		else{
		$page_url = $value;		
		}		
	}
	if($field=="og_description"){
		$page_desc = $value;	
	}
     }

     $ID_temp = str_replace("/Shared-ubuntu/solr-7.7.0/latimes/", "", $ID);
     if($page_url=="N/A"){
     	try{
		$fileHandler = fopen("URLtoHTML_latimes_news.csv","r");
		
		while($line=fgetcsv($fileHandler,0,",")){
			if($ID_temp==$line[0]){
				$page_url = $line[1];
				break;			
			}		
		}
		fclose($fileHandler);
	
	}catch(Exception $e){
		echo "Exception Encountered";	
	}
     }
?>
          <tr>
            <th><?php echo "ID" ?></th>
            <td><?php echo htmlspecialchars($ID, ENT_NOQUOTES, 'utf-8'); ?><?php echo "</br>"?> </td>
          </tr>
	  <tr>
            <th><?php echo "Title" ?></th>
            <td><?php echo "<a href='".$page_url."' target='_blank'>".htmlspecialchars($title, ENT_NOQUOTES, 'utf-8')."</a>"; ?><?php echo "</br>"?> </td>
          </tr>
	  <tr>
            <th><?php echo "URL" ?></th>
            <td><?php echo "<a href='".$page_url."' target='_blank'>".htmlspecialchars($page_url, ENT_NOQUOTES, 'utf-8')."</a>"; ?><?php echo "</br>"?> </td>
          </tr>
	  <tr>
            <th><?php echo "Description" ?></th>
            <td><?php echo htmlspecialchars($page_desc, ENT_NOQUOTES, 'utf-8'); ?><?php echo "</br>"?> </td>
          </tr>
        </table>
      </li>
<?php
  }
?>
    </ol>
<?php
}
?>
   </center>
  </body>
</html>

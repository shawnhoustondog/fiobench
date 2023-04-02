<?php
  session_start(); 
?>
<html>
  <head>
    <title>FIO Benchmark</title>
    <style type="text/css">
      div.upload-wrapper {
        color: white;
        font-weight: bold;
        display: flex;
      }

      input[type="file"] {
        position: absolute;
        left: -9999px;
      }

      input[type="submit"] {
        border: 3px solid #555;
        color: white;
        background: #666;
        margin: 10px 0;
        border-radius: 5px;
        font-weight: bold;
        padding: 5px 20px;
        cursor: pointer;
      }

      input[type="submit"]:hover {
        background: #555;
      }

      label[for="file-upload"] {
        padding: 0.7rem;
        display: inline-block;
        background: #fa5200;
        cursor: pointer;
        border: 3px solid #ca3103;
        border-radius: 0 5px 5px 0;
        border-left: 0;
      }
      label[for="file-upload"]:hover {
        background: #ca3103;
      }

      span.file-name {
        padding: 0.7rem 3rem 0.7rem 0.7rem;
        white-space: nowrap;
        overflow: hidden;
        background: #ffb543;
        color: black;
        border: 3px solid #f0980f;
        border-radius: 5px 0 0 5px;
        border-right: 0;
      }
    </style>

  </head>
  <body>

    <h1>FIO Benchmark</h1>
  
<?php 

// phpinfo(); 

  function getbytesfrombw($input)
  {
    $retval = 0;
    if(preg_match('/([\d\.]+)([TGMK])/', $input, $parts))
    {
      $retval = $parts[1];
      switch ($parts[2])
      {
        case "T":
          $retval *= 1024.0;
        case "G":
          $retval *= 1024.0;
        case "M":
          $retval *= 1024.0;
        case "K":
          $retval *= 1024.0;
        default:
          break;
      }
    }
    return round($retval, 2);
  }

  function getbwfrombytes($input)
  {
    $counter = 0;
    $retval = $input;
    while($retval > 1024.0)
    {
      $retval /= 1024.0;
      ++$counter;
    }
    $retval = round($retval, 1);
    switch ($counter)
    {
      case 4:
        $retval .= "TiB/s";
        break;
      case 3:
        $retval .= "GiB/s";
        break;
      case 2:
        $retval .= "MiB/s";
        break;
      case 1:
        $retval .= "KiB/s";
        break;
      case 0:
        $retval .= "B/s";
        break;
      default:
        $retval .= "??iB/s";
        break;
    }
    return $retval;
  }
  
  // Function to calculate square of value - mean
  function sd_square($x, $mean) { return pow($x - $mean,2); }

  // Function to calculate standard deviation (uses sd_square)    
  function sd($array) {
      // square root of sum of squares devided by N-1
      if(count($array) == 1) { return 0; }
      return sqrt(array_sum(array_map("sd_square", $array, array_fill(0,count($array), (array_sum($array) / count($array)) ) ) ) / (count($array)-1) );
  }
  
  $uploadFileDir = '/opt/app-root/files/';
  $files = scandir($uploadFileDir);
  $fio_data = array();
  $fio_clients = 0;
  foreach($files as &$file)
  {
    $match = false;
    if($file != '.' && $file != '..')
    {
      $rows = explode("\n", file_get_contents("$uploadFileDir/$file"));
      foreach($rows as $row)
      {
        if( preg_match('/\s+read:\s+IOPS=(\d+),\s+BW=([^\s]+).+/', $row, $row_data) )
        {
          $fio_data[$fio_clients]["read"]["IOPS"] = $row_data[1];
          $fio_data[$fio_clients]["read"]["BW"] = $row_data[2];
          $match = true;
        }
        if( preg_match('/\s+write:\s+IOPS=(\d+),\s+BW=([^\s]+).+/', $row, $row_data) )
        {
          $fio_data[$fio_clients]["write"]["IOPS"] = $row_data[1];
          $fio_data[$fio_clients]["write"]["BW"] = $row_data[2];
          $match = true;
        }
      }
    }
    if($match)
    {
      ++$fio_clients;
    }
  }
  
  /// time to do some matching and math...
  $readIOPS = array();
  $writeIOPS = array();
  $readBW = array();
  $writeBW = array();
  foreach($fio_data as $row_num => $subarray)
  {
    if(array_key_exists("read", $subarray))
    {
      if(array_key_exists("IOPS", $subarray["read"])) { array_push($readIOPS, $subarray["read"]["IOPS"]); }
      if(array_key_exists("BW", $subarray["read"])) { array_push($readBW, getbytesfrombw($subarray["read"]["BW"])); }
    }
    if($subarray["write"])
    {
      if(array_key_exists("IOPS", $subarray["write"])) { array_push($writeIOPS, $subarray["write"]["IOPS"]); }
      if(array_key_exists("BW", $subarray["write"])) { array_push($writeBW, getbytesfrombw($subarray["write"]["BW"])); }
    }
  }  
  print "    <p>fio clients: " . $fio_clients . "</p>\n";

  if(count($readIOPS))
  {
    print "    <p>Total read IOPS = " . array_sum($readIOPS) . "<br />\n";
    print "    &nbsp&nbsp&nbsp Mean = " . round(array_sum($readIOPS)/count($readIOPS), 1) . "<br />\n";
    print "    &nbsp&nbsp&nbsp Standard Deviation = " . round(sd($readIOPS), 1) . "</p>\n";
  }

  if(count($readBW))
  {
    print "    <p>Total read BW = " . getbwfrombytes(array_sum($readBW)) . "<br />\n";
    print "    &nbsp&nbsp&nbsp Mean = " . getbwfrombytes(array_sum($readBW)/count($readBW)) . "<br />\n";
    print "    &nbsp&nbsp&nbsp Standard Deviation = " . getbwfrombytes(sd($readBW)) . "</p>\n";
  }

  if(count($writeIOPS))
  {
    print "    <p>Total write IOPS = " . array_sum($writeIOPS) . "<br />\n";
    print "    &nbsp&nbsp&nbsp Mean = " . round(array_sum($writeIOPS)/count($writeIOPS), 1) . "<br />\n";
    print "    &nbsp&nbsp&nbsp Standard Deviation = " . round(sd($writeIOPS), 1) . "</p>\n";
  }

  if(count($writeBW))
  {
    print "    <p>Total write BW = " . getbwfrombytes(array_sum($writeBW)) . "<br />\n";
    print "    &nbsp&nbsp&nbsp Mean = " . getbwfrombytes(array_sum($writeBW)/count($writeBW)) . "<br />\n";
    print "    &nbsp&nbsp&nbsp Standard Deviation = " . getbwfrombytes(sd($writeBW)) . "</p>\n";
  }

  print "    <pre>\n" . file_get_contents("/configs/fio.job") . "\n    </pre>\n";
  
      if (isset($_SESSION['message']) && $_SESSION['message'])
      {
        echo '    <p class="notification">'.$_SESSION['message'].'</p>';
        unset($_SESSION['message']);
      }

?>

    <h2>Fio Output Files</h2>
    <p>

<?php
  $uploadFileDir = '/opt/app-root/files/';
  $files = scandir($uploadFileDir);
  foreach($files as &$file)
  {
    if($file != '.' && $file != '..')
    {
      print "    <a href=\"download.php?file=$file\">$file</a> <br />\n";
    }
  }
?>
    </p>

  </body>
</html>






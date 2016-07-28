<?php
  $cid = $_GET['cid'];
  $link = "https://cabinet.spbu.ru/Lists/1k_EntryLists/list_$cid.html";
  if ($_GET['link']) $link=$_GET['link'];
 if ($_GET['orig']=='on') $orig='yes';
  else $orig='no';
  if ($_GET['first']=='on') $first='yes';
   else $first='no';
  if ($_GET['nobvi']=='on') $bvi='no';
   else $bvi='yes';
if ($_GET['prSort']=='on') $prSort='yes';
  else $prSort='no';
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta charset="UTF-8">
    </head>
    <body>
      <style>
        table {
          border-collapse: collapse;
        }
      </style>

      <ul style="float: right;">
        <li><a href="/rating/spbu?cid=b83a1ed5-8d4b-4ecb-86cb-c81762b25d0e">Матобес</a></li>
        <li><a href="/rating/spbu?cid=2f51dacc-f17a-4c72-b623-2f0525cbb88a">ПМИ (матмех)</a></li>
        <li><a href="/rating/spbu?cid=97a05ded-f28d-4929-aead-b6dc7cfc9f99">ПМИ (ПМ-ПУ)</a></li>
        <li><a href="/rating/spbu?cid=f8ec88ea-4c17-420d-a80b-3bdce26deda6">ПМФ</a></li>
        <li><a href="/rating/spbu?cid=462f7f85-e9fc-4535-a90b-b4562766bbc7">Физика</a></li>
      </ul>

        <form method="GET" action="">
          <input autocomplete="off" name="link" style="width:600px;" type='text' placeholder='Ссылка на страницу с конкурсом' value="<?php if ($link!="https://cabinet.spbu.ru/Lists/1k_EntryLists/list_.html") echo $link; ?>" /><br />
          <input name="orig" type="checkbox"<?php if ($orig=='yes') echo " checked"; ?>>Только с оригиналами аттестатов</input><br />
          <input name="nobvi" type="checkbox"<?php if ($bvi=='no') echo " checked"; ?>>Только общий конкурс</input><br />
          <input name="first" type="checkbox"<?php if ($first=='yes') echo " checked"; ?>>Только первый приоритет</input><br />
          <input name="prSort" type="checkbox"<?php if ($prSort=='yes') echo " checked"; ?>>Сортировать по приоритету</input><br />
          <button type='submit'>Применить</button>
        </form>

          <?php
        if ($link!="https://cabinet.spbu.ru/Lists/1k_EntryLists/list_.html") {
          $html = file_get_contents($link);
          preg_match('|<body[^>]*?>(.*?)<table|sei', $html, $head);
          $dog=false;
          if (strpos($head[1], 'Договорная')) $dog=true;
          echo $head[1];
          if ($bvi=='no') {
            ?>
              Число поступивших БВИ: <b><?php echo $count = preg_match_all('|б/э|sei', $html, $head); ?></b>
            <?php
          }
          ?>
          <table border="1">
<thead>
 <tr>
  <th>№ п/п</th>
  <th>Рег. номер</th>
  <th>Фамилия Имя Отчество</th>
  <th>Дата рождения</th>
  <th>Тип конкурса</th>
  <?php if (!$dog) { ?><th>Приоритет</th><?php } ?>
  <th>Сумма баллов (общая)</th>
  <th>Сумма баллов (осн)</th>
<th> Предмет 1 (Математика)</th>
<th> Предмет 2 (Информатика и ИКТ)</th>
<th> Предмет 3 (Русский язык)</th>
  <th>Сумма доп. баллов</th>
  <th>Оригиналы</th>
  <th>Индивидуальные достижения</th>
  <th>Примечания</th>
 </tr>
</thead>
<tbody><?php

          if ($prSort=="yes") {
              $pr1="";
              $pr2="";
              $pr3="";
              $pr4="";
          }

          preg_match_all("|<tr[^>]*?>(.*?)</tr>|sei", $html, $matches);
          $i=0;
foreach($matches[0] as $tr)
	{
    if ($i!=0) {
      preg_match_all("|<td[^>]*?>(.*?)</td>|sei", $tr, $fields);
      $j=1;
      foreach ($fields[0] as $td) {
          if ($j==6) {
            if ($td=="<td>1</td>") $firstPr=true;
            else $firstPr=false;
            if ($td=="<td>1</td>") $pr=1;
            if ($td=="<td>2</td>") $pr=2;
            if ($td=="<td>3</td>") $pr=3;
            if ($td=="<td>4</td>") $pr=4;
          }
          if ($j==5) {
            if ($td=="<td>б/э</td>") $hasbvi=true;
            else $hasbvi=false;
          }
          if ($j==13) {
            if ($td=="<td>Да</td>") $hasOrig=true;
            else $hasOrig=false;
          }
          $j++;
      }
      if (($orig=='no' || ($orig=='yes' && $hasOrig==true)) && ($bvi=='yes' || ($bvi=='no' && $hasbvi==false)) && ($first=='no' || ($first=='yes' && $firstPr==true))) {
        if ($prSort=='no') echo preg_replace("!<td>(.*?)</td>!si","<td>$i</td>",$tr,1);
        else {
          if ($pr==1) $pr1.=preg_replace("!<td>(.*?)</td>!si","<td>$i</td>",$tr,1);
          if ($pr==2) $pr2.=preg_replace("!<td>(.*?)</td>!si","<td>$i</td>",$tr,1);
          if ($pr==3) $pr3.=preg_replace("!<td>(.*?)</td>!si","<td>$i</td>",$tr,1);
          if ($pr==4) $pr4.=preg_replace("!<td>(.*?)</td>!si","<td>$i</td>",$tr,1);
        }
        $i++;
      }
    }
    if ($i==0) $i++;
  }
  if ($prSort=="yes") echo $pr1.$pr2.$pr3.$pr4;
  ?>
</tbody>
</table>
  <?php
        }
      ?>
    </body>
</html>

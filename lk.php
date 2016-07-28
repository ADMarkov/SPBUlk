<?php

    $surname=$_GET['surname'];
    $name=$_GET['name'];
    $mname=$_GET['mname'];
    $nomname=$_GET['nomname'];
    if (!$nomname) $nomname='no';
    $bday=$_GET['bday'];
    $bmonth=$_GET['bmonth'];
    $byear=$_GET['byear'];
    $fio=$surname.' '.$name.' ';
    if ($nomname=='no') $fio.=$mname;
    $birthday=$bday.'.'.$bmonth.'.'.$byear;

    //Запись посещения в БД, сбор статистики
    $data=$surname.' '.$name.' '.$mname.' '.$birthday;
    $ip=$_SERVER['REMOTE_ADDR'];
    $db = mysqli_connect(/*Здесь данные для подключения к БД*/);
    mysqli_query($db, "INSERT INTO `gb_admarkov3`.`spbulkvisiters` (`ip`, `data`) VALUES ('".$ip."','".$data."');");

    function findPosition($uid, $cid, $orig, $dog) {
        $html=file_get_contents("https://cabinet.spbu.ru/Lists/1k_EntryLists/list_".$cid.".html");
        preg_match_all("|<tr[^>]*?>(.*?)</tr>|sei", $html, $matches);
        $i=0;
        $origpos=0;
foreach($matches[0] as $tr)
{
    $me=false;
    preg_match_all("|<td[^>]*?>(.*?)</td>|sei", $tr, $fields);
    $j=1;
    foreach ($fields[0] as $td) {
        if ($j==3) {
          if (preg_match('|<a name="'.$uid.'"></a>|',$td)) $me=true;
          if ($me && $orig==false) return $i;
          if ($dog) $j++;
        }
        if ($j==13) {
          $hasorig=false;
          if ($td=="<td>Да</td>") {
            $origpos++;
            $hasorig=true;
          }
        }
        $j++;
    }
    if ($me) {
      if ($hasorig) return $origpos;
      else return $origpos+1;
    }
  $i++;
}


    }

?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta charset="UTF-8">
    </head>
    <body>

      <style>
      .postImg {
        height: 42px;
        position: relative;
        top: 12px;
      }
      h1 {
        font-family: Helvetica;
        margin-top: 0;
      }
      </style>
      <div style="float: right;">
        <a href="http://vk.com/a.d.markov">Александр Марков</a>, 2016 &copy;
      </div>

      <h1>Личный кабинет абитуриента СПбГУ <a href="//vk.com/postypashki"><img src="/files/images/postupashki.jpg" title="При поддержке vk.com/postypashki" class="postImg"/></a></h1>
      <form method="GET" action="">
      <div>
        <input name="surname" type="text" placeholder="Фамилия" value="<?php echo $surname; ?>"/>
        <input name="name" type="text" placeholder="Имя" value="<?php echo $name; ?>"/>
        <input name="mname" type="text" placeholder="Отчество" value="<?php echo $mname; ?>"/>
        (<input name="nomname" type="checkbox" <?php if ($nomname=="on") echo "checked"; ?>>Нет отчества</input>)
      </div>
      <div style="margin-top: 4px;">
        Дата рождения: <input name="bday" type="text" style="width: 20px;" placeholder="ДД" value="<?php echo $bday; ?>"/><b>.</b><input name="bmonth" type="text" style="width: 20px;" placeholder="ММ" value="<?php echo $bmonth; ?>"/><b>.</b><input name="byear" type="text" style="width: 40px;" placeholder="ГГГГ" value="<?php echo $byear; ?>"/>
        <button type="submit">Найти</button>
      </div>
      </form>

      <style>
        img {
          display: inline;
        }
      </style>
      <br />
      <a href="http://xn--80aizdehccdj1m.xn--p1ai/turnik-3v1-monolit-flagman-nastennyj-razbornyj"><img height="180" src="/sandbox/SPBUlk/ko2.jpg" /><img height="180" src="/sandbox/SPBUlk/ko3.jpg" /></a>&nbsp;&nbsp;<a href="http://xn--80aizdehccdj1m.xn--p1ai/turnik-3v1-monolit-flagman-nastennyj-razbornyj"><img height="180" src="/sandbox/SPBUlk/ko1.jpg" /><img height="180" src="/sandbox/SPBUlk/ko4.jpg" /></a>
      <p><b>
        Поступил абитура? - Маладца. Не бывает учёбы без здоровья, позаботься о нём, твой лечащий доктор <a href="http://xn--80aizdehccdj1m.xn--p1ai/turnik-3v1-monolit-flagman-nastennyj-razbornyj">Турник 3 в 1 «Монолит Флагман»</a>.<br />Доставим и установим.
      </b></p>

      <?php
        $listHTML=file_get_contents("https://cabinet.spbu.ru/Lists/1k_EntryLists/");
        $found=false;
        preg_match_all("|<tr[^>]*?>(.*?)</tr>|sei", $listHTML, $table);
        $id='0';

        foreach ($table[0] as $tr) {
          if ($id!='0') break;
          preg_match_all("|<td[^>]*?>(.*?)</td>|sei", $tr, $fields);
          $trueFIO=false;
          $trueBDAY=false;
          $i=1;
          foreach ($fields[0] as $td) {
            if ($i==3) {
            if ($td=='<td width="23%">'.$fio."</td>") $trueFIO=true;
           }
           if ($i==4) {
            if ($td=='<td width="15%">'.$birthday."</td>") $trueBDAY=true;
          }
          if ($i==5 && $trueBDAY && $trueFIO) {
            preg_match("|GetPersonEntries\('(.*?)'\)|", $td, $res);
            $id=$res[1];
          }
           $i++;
          }
        }
        if ($id!='0') {
        $data = file_get_contents("https://cabinet.spbu.ru/Lists/1k_EntryLists/data/$id.txt");
        preg_match_all("|<p[^>]*?>(.*?)</p>|sei", $data, $mp);
        foreach($mp[0] as $p) {
          $dog=false;
          if (strpos($p, 'Договорная')) $dog=true;
          preg_match('|href\="list_(.*?)\.html\#(.*?)">|', $p, $res);
          echo preg_replace('|href\="list_(.*?)\.html\#(.*?)">Просмотр конкурса</a>   </p>|','href="http://admarkov.ru/rating/spbu?cid='.$res[1].'">Просмотр конкурса (с фильтром)</a>',$p)."<br />";
          echo "Позиция (без учета аттестата): <b>".findPosition($res[2], $res[1], false, $dog)."</b><br />";
          echo "Позиция (с учетом аттестата): <b>".findPosition($res[2], $res[1], true, $dog)."</b>";
          echo "</p>";
        }
       }
      ?>

    </body>
</html>

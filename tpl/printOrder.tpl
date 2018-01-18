<HTML xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<TITLE><?php echo $this->title; ?></TITLE> 

<META HTTP-EQUIV="Content-Type" CONTENT="text/html; windows-1251">
<link href="<?php echo $this->css; ?>" rel="stylesheet" type="text/css">

</HEAD>
<BODY>

<div id="university">БЕЛОРУССКИЙ ГОСУДАРСТВЕННЫЙ МЕДИЦИНСКИЙ УНИВЕРСИТЕТ</div>

<div id="header1"><span id="department"><?php echo $this->data['adduserid']; ?></span> ОРДЕР №<span id="order"><?php echo $this->data['hor']; ?></span></div>

<div id="header1">на жилое помещение в общежитии</div>

<div id="student">Студенту</div><div id="printname"><?php echo $this->data['fio']; ?></div>

<div id="footer">
    <p>предоставлено место место в комнате №________ обжежития № ________</p>
    <p>Ордер выдан на основании приказа ректора
        №<span id="zaselenie"><?php echo $this->data['zas']; ?></span> от <span id="zasdate"><?php echo $this->data['zasdate']; ?> г.</span</p>
    <p>Декан факультета ____________________________________<?php echo $this->data['dekan']; ?></p>
    <p>Директор студгородка ________________________________ А.Ю. Катичев</p>
</div>

<p id="date"><?php echo $this->data['hordate']; ?></p>

</BODY>
</HTML>
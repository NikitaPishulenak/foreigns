<HTML xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<TITLE><?php echo $this->title; ?></TITLE> 

<META HTTP-EQUIV="Content-Type" CONTENT="text/html; windows-1251">
<link href="<?php echo $this->css; ?>" rel="stylesheet" type="text/css">

</HEAD>
<BODY>

<div id="university">����������� ��������������� ����������� �����������</div>

<div id="header1"><span id="department"><?php echo $this->data['adduserid']; ?></span> ����� �<span id="order"><?php echo $this->data['hor']; ?></span></div>

<div id="header1">�� ����� ��������� � ���������</div>

<div id="student">��������</div><div id="printname"><?php echo $this->data['fio']; ?></div>

<div id="footer">
    <p>������������� ����� ����� � ������� �________ ��������� � ________</p>
    <p>����� ����� �� ��������� ������� �������
        �<span id="zaselenie"><?php echo $this->data['zas']; ?></span> �� <span id="zasdate"><?php echo $this->data['zasdate']; ?> �.</span</p>
    <p>����� ���������� ____________________________________<?php echo $this->data['dekan']; ?></p>
    <p>�������� ����������� ________________________________ �.�. �������</p>
</div>

<p id="date"><?php echo $this->data['hordate']; ?></p>

</BODY>
</HTML>
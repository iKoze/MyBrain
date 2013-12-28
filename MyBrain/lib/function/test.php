<?php

include('interface/IBasicDatabase.php');
include('interface/IBasicHash.php');
include('class/coreutil/TextFile.php');
include('class/optutil/FileDB.php');
include('class/optutil/BlowFishHash.php');
include('class/optutil/UserManagement.php');

$f = new FileDB("./test");
/*
print $f->getRoot()."\n";
$f->saveValue(array("this","is","test"),"Hello a World!");
print $f->getSubPathByRl(array("this","is","test"),0)."\n";
$f->saveObject(array("self"),array("hi","ho"));
var_dump($f->getObject(array("self")));

$o = $f->chroot(array("this"));
print $o->getValue(array("is","test"))."\n";

$o->saveValue(array("hi.mio &"," "),"test0r");
print $o->getValue(array("hi.mio &"));*/
$h = new BlowFishHash();
$u = new UserManagement($f, $h);
$u->newUser("test","hiho") ? print "angelegt": print "nicht angelegt";

$u->authenticate("test","hiho") ? print "ja" : print "nein";

$userdb = $u->getUserDb("test");
$userdb->saveValue(array("new"),"test");




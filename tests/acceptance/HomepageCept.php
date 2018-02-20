<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('See Anonymous homepage');
$I->amOnPage('/');
$I->seeElement(".mpicon .img-responsive");
$I->cantSee('$text');
$I->cantSee('error');
$I->cantSee('warning');
$I->cantSee('notice');

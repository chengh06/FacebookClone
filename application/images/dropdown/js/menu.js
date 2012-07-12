// JavaScript Document
var MyVar=1;

// First menu
var menuModel = new DHTMLSuite.menuModel();
DHTMLSuite.commonObj.setCssCacheStatus(false);


menuModel.addItem(1,'New','','',false,'','');
menuModel.addItem(2,'Edit','','',false);
menuModel.addSeparator();

menuModel.addItem(3,'Save','../images/disk.gif','',false,'Save your work','saveWork()');
menuModel.addItem(10,'','../images/print.gif','',false,'Print page');

menuModel.addItem(11,'Open','../images/open.gif','',false,'Open document','');
menuModel.setSubMenuWidth(11,120);
menuModel.addItem(111,'RTF','','',11,'Open document','');
menuModel.addItem(112,'PDF','','',11,'Open document','');
menuModel.addItem(114,'Spreadsheet','','',11,'Open document','');
menuModel.addSeparator(11);

menuModel.addItem(113,'Template','','',11,'Open document','');
menuModel.setSubMenuWidth(113,100);
menuModel.addItem(1131,'RTF','','',113,'Open document','');
menuModel.addItem(1132,'PDF','','',113,'Open document','');

menuModel.addItem(1133,'DOC','','',113,'Open document','');


menuModel.addItem(4,'Tools','','',false);
menuModel.setSubMenuWidth(4,155);
menuModel.addItem(5,'Settings','','',4);
menuModel.addItem(6,'Internet Options','','',4);

menuModel.addItem(40,'Help','','',false);
menuModel.setSubMenuWidth(40,130);
menuModel.addItem(41,'About','../images/open.gif','',40);
menuModel.addItem(42,'Index','','',40);
menuModel.addItem(43,'Support','','',40);
menuModel.setSubMenuWidth(43,130);
menuModel.addItem(431,'Contact','','',43);
menuModel.setSubMenuWidth(431,130);
menuModel.addItem(4311,'Manager','','',431);
menuModel.addItem(4312,'Support','','',431);
menuModel.addItem(4313,'Finance dep.','','',431);
menuModel.addItem(4314,'Account man.','','',431);
menuModel.addItem(432,'Email','','',43);



menuModel.init();

var menuBar = new DHTMLSuite.menuBar();
menuBar.addMenuItems(menuModel);
menuBar.setTarget('menuBarContainer');
menuBar.init();

/* This is a menu model which will be appended to the menuBar dynamically */
var menuModel_rep1 = new DHTMLSuite.menuModel();
menuModel_rep1.addItem(9001,'Mail message','','',false);
menuModel_rep1.addItem(9002,'Template','','',false);
menuModel_rep1.addItem(90021,'Mail template','','',9002);
menuModel_rep1.addItem(90022,'Appointment','','',9002);
menuModel_rep1.setSubMenuWidth(9002,130);
menuModel_rep1.init();

var menuModel_repl2 = new DHTMLSuite.menuModel();
menuModel_repl2.addItem(8000,'Appointment','','',false);
menuModel_repl2.addItem(8001,'Calendar entry','','',false);
menuModel_repl2.addSeparator();
menuModel_repl2.addItem(8002,'Template','','',false);
menuModel_repl2.addItem(80011,'HTML','','',8002);
menuModel_repl2.addItem(80012,'PDF','','',8002);
menuModel_repl2.addItem(80013,'RTF','','',8002);
menuModel_repl2.addItem(80014,'HTML','','',8002);
menuModel_repl2.addItem(80015,'Plain text','','',8002);
menuModel_repl2.setSubMenuWidth(8002,130);
menuModel_repl2.init();


var menuModel_rep3 = new DHTMLSuite.menuModel();
menuModel_rep3.addItem(7001,'DHTMLGoodies','','',false);
menuModel_rep3.addItem(7002,'Acme','','',false);
menuModel_rep3.addItem(70011,'Alf Magne','','',7001);
menuModel_rep3.setSubMenuWidth(7002,130);
menuModel_rep3.addItem(70021,'Name 1','','',7002);
menuModel_rep3.addItem(70022,'Name 2','','',7002);
menuModel_rep3.setSubMenuWidth(7001,130);
menuModel_rep3.init();

/* Another menu model */
var menuModel2 = new DHTMLSuite.menuModel();
menuModel2.addItem(201,'File','../images/disk.gif','',false);
menuModel2.addItem(2011,'Save','../images/disk.gif','',201);
menuModel2.addItem(2012,'Open','../images/open.gif','',201);
menuModel2.addSeparator(201);
menuModel2.addItem(2013,'Save as','','',201);


menuModel2.addItem(202,'Edit','','',false);
menuModel2.setSubMenuWidth(201,130);
menuModel2.addItem(2021,'Document','','',202);
menuModel2.addItem(2022,'Template','','',202);


menuModel2.addItem(203,'Search','','',false);
menuModel2.addItem(204,'View','','',false);
menuModel2.addItem(205,'Project','','',false);
menuModel2.setSubMenuWidth(205,130);
menuModel2.addItem(206,'New project','','',205);
menuModel2.addItem(207,'Open project','','',205);
menuModel2.setSubMenuWidth(207,130);
menuModel2.addItem(2071,'Open project 2','','',207);
menuModel2.addItem(2072,'Open project 3','','',207);



menuModel2.setSubMenuWidth(2013,100);
menuModel2.addItem(20131,'PDF','','',2013);
menuModel2.addItem(20132,'RTF','','',2013);
menuModel2.addItem(20133,'HTML','','',2013);
menuModel2.setSubMenuWidth(20133,130);

menuModel2.addItem(201331,'With styles','','',20133);
menuModel2.addItem(2013311,'Rich formatted','','',201331);
menuModel2.addItem(2013312,'HTML Strict','','',201331);
menuModel2.addItem(2013313,'HTML loose','','',201331);
menuModel2.addItem(201332,'Without styles','','',20133);


menuModel2.init();

var menuBar2 = new DHTMLSuite.menuBar();
menuBar2.addMenuItems(menuModel2);
menuBar2.setTarget('otherMenu');
menuBar2.init();


menuBar2.setMenuItemState(2013,'disabled');	// Disable menu item "Save As" 
menuBar2.setMenuItemState(202,'disabled');	// Disable menu item "Edit" 

/* Third menu model */
var menuModel3 = new DHTMLSuite.menuModel();
menuModel3.addItem(5201,'File','../images/disk.gif','',false);
menuModel3.addItem(5202,'Edit','','',false);
menuModel3.addItem(5203,'Search','','',false);
menuModel3.addItem(5204,'View','','',false);
menuModel3.addItem(5205,'Project','','',false);
menuModel3.init();

var menuBar3 = new DHTMLSuite.menuBar();
menuBar3.addMenuItems(menuModel3);
menuBar3.setTarget('thirdMenu');
menuBar3.init();

menuBar3.setMenuItemState(5202,'disabled');

// Fourth menu

var menuModel4 = new DHTMLSuite.menuModel();
menuModel4.setSubMenuType(1,'sub');
menuModel4.addItem(15201,'File','../images/disk.gif','',false);
menuModel4.setSubMenuWidth(15201,130);
menuModel4.addItem(152012,'Open','../images/open.gif','',15201);
menuModel4.addItem(152011,'Save','','',15201);
menuModel4.addItem(152013,'Save As','','',15201);


menuModel4.addItem(15202,'Edit','','',false);
menuModel4.addItem(15203,'Search','','',false);
menuModel4.addItem(15204,'View','../images/open.gif','',false);
menuModel4.addSeparator();

menuModel4.setSubMenuWidth(15204,130);
menuModel4.addItem(152041,'As HTML','','',15204);
menuModel4.addItem(152042,'As Plain text','','',15204);
menuModel4.addItem(15205,'Project','','',false);
menuModel4.addItem(15206,'DHTMLGoodies','','http://www.dhtmlgoodies.com',false);
menuModel4.setSubMenuWidth(15205,130);
menuModel4.addItem(152051,'Open project','','',15205);
menuModel4.addItem(152052,'Save project','','',15205);
menuModel4.addItem(152053,'New project','','',15205);
menuModel4.setMainMenuGroupWidth(200);	


menuModel4.init();

var menuBar4 = new DHTMLSuite.menuBar();
menuBar4.addMenuItems(menuModel4);
menuBar4.setTarget('fourthmenu');
menuBar4.setActiveSubItemsOnMouseOver(true);
menuBar4.init();

menuBar4.setMenuItemState(15204,'disabled');


/* Fifth menu - created fro markup */
var menuModel5 = new DHTMLSuite.menuModel();
menuModel5.addItemsFromMarkup('menuModel5');
menuModel5.init();

var menuBar5 = new DHTMLSuite.menuBar();
menuBar5.addMenuItems(menuModel5);
menuBar5.setTarget('fifthMenu');
menuBar5.init();


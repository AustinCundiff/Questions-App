var msgInput=new Array();
var session;

//MSG_NULL
msgInput[0]='{\n\
	'+session+'\n\
	"msgId":0,\n\
}';


//MSG_LOGIN
msgInput[1]='{\n\
	"msgId":1,\n\
	"ver":1,\n\
	"username":"Trent",\n\
	"pass":"pieIsGood",\n\
	"debugOn":1\n\
}';


//MSG_CREATQUES
msgInput[2]='{\n\
	'+session+'\n\
	"msgId":2,\n\
	"title":"Why are there spots on my goldfish?",\n\
	"category":1,\n\
	"qaParts": [\n\
		{\n\
			"partType":1,\n\
			"text":"My goldfish recently got these white spots all over it. I haven’t changed anything about the tank. Does anyone know what the problem could be?"\n\
		},\n\
		{\n\
			"partType":2,\n\
			"fileName":"file0",\n\
			"thumbnail":1,\n\
		},\n\
		{\n\
			"partType":3,\n\
			"fileName":"file1"\n\
		},\n\
		{\n\
			"partType":4,\n\
			"fileName":"file2"\n\
		}\n\
	]\n\
}';


//MSG_GETQUESLIST
msgInput[3]='{\n\
	'+session+'\n\
	"msgId":3,\n\
	"category":1,\n\
	"search":"fish",\n\
	"sortOrder":1,\n\
	"continuePrev":0\n\
}';


//MSG_GETQUES
msgInput[4]='{\n\
	'+session+'\n\
	"msgId":4,\n\
	"questionId":1,\n\
	"listParts":1\n\
	"listAnswers":1\n\
}';

//MSG_CREAT_ANS
msgInput[5]='{\n\
	'+session+'\n\
	"msgId":5,\n\
	"questionId":1,\n\
	"title":"Your goldfish has ich",\n\
	"qaParts": [\n\
		{\n\
			"partType":1,\n\
			"text":"Ich is a common fungus that affects goldfish. You should try some medicine like ich remover plus or happy goldfish ich cure."\n\
		},\n\
		{\n\
			"partType":2,\n\
			"fileName":"file0",\n\
			"thumbnail":1\n\
		},\n\
		{\n\
			"partType":3,\n\
			"fileName":"file1"\n\
		},\n\
		{\n\
			"partType":4,\n\
			"fileName":"file2"\n\
		}\n\
	]\n\
}';


//MSG_GETANSLIST
msgInput[6]='{\n\
	'+session+'\n\
	"msgId":6,\n\
	"questionId":1,\n\
	"continuePrev":0\n\
}';


//MSG_GETANS
msgInput[7]='{\n\
	'+session+'\n\
	"msgId":7,\n\
	"answerId":1,\n\
	"listParts":1\n\
}';


//MSG_VOTEANS
msgInput[8]='{\n\
	'+session+'\n\
	"msgId":8,\n\
	"answerId":1,\n\
	"vote":1\n\
}';


//MSG_VOTEBEST
msgInput[9]='{\n\
	'+session+'\n\
	"msgId":9,\n\
	"answerId":1\n\
}';

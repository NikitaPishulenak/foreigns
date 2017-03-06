Что необходимо изменить в вашей БД?!!!
1. Добавить поля в iws_foreigners
    	formNumber VARCHAR(15) NULL после invdate
	после поля status: whoInvites VARCHAR(250) NULL
			   actionEndDate DATE NULL
			   note TEXT NULL
2. У нас в задание один из пунктов был ликвидировать колонку. С ней связано 2 поля: pet и petdate. Я пока их не удалял. Пусть может побутут пока, вдруг что))
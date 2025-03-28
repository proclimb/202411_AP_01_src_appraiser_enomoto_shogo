<?php
//
//物件管理リスト
//
function fnSqlArticleList($flg, $sDel, $sArticle, $sRoom, $sKeyPlace, $sArticleNote, $sKeyBox, $sDrawing, $sSellCharge, $sPage, $orderBy, $orderTo)
{
	switch ($flg) {
		case 0:
			$sql  = "SELECT COUNT(*)";
			break;
		case 1:
			$sql  = "SELECT ARTICLENO, ARTICLE, ROOM, KEYPLACE, ARTICLENOTE, KEYBOX, DRAWING, SELLCHARGE";
			break;
	}
	$sql .= " FROM TBLARTICLE";
	$sql .= " WHERE DEL = $sDel";
	if ($sArticle) {
		$sArticle = htmlspecialchars_decode($sArticle, ENT_QUOTES);
		$sql .= " AND ARTICLE LIKE '%$sArticle%'";
	}
	if ($sRoom) {
		$sRoom = htmlspecialchars_decode($sRoom, ENT_QUOTES);
		$sql .= " AND ROOM LIKE '%$sRoom%'";
	}
	if ($sKeyPlace) {
		$sKeyPlace = htmlspecialchars_decode($sKeyPlace, ENT_QUOTES);
		$sql .= " AND KEYPLACE LIKE '%$sKeyPlace%'";
	}
	if ($sArticleNote) {
		$sArticleNote = htmlspecialchars_decode($sArticleNote, ENT_QUOTES);
		$sql .= " AND ARTICLENOTE LIKE '%$sArticleNote%'";
	}
	if ($sKeyBox) {
		$sKeyBox = htmlspecialchars_decode($sKeyBox, ENT_QUOTES);
		$sql .= " AND KEYBOX LIKE '%$sKeyBox%'";
	}
	/*if ($sDrawing) {
		$sDrawing = htmlspecialchars_decode($sDrawing, ENT_QUOTES);
		$sql .= " AND DRAWING LIKE '%$sDrawing%'";
	}*/
	/* 複数選択された場合に対応するSQLを作成する
	if (!empty($sDrawing)) {
		// htmlspecialchars_decode を配列内の各要素に適用
		$sDrawing = array_map('htmlspecialchars_decode', $sDrawing);

		// SQLクエリの条件部分を作成
		$conditions = [];
		foreach ($sDrawing as $value) {
			// HTMLエスケープ処理を行う
			$value = htmlspecialchars($value, ENT_QUOTES);
			// 各条件を配列に追加
			$conditions[] = "DRAWING LIKE '%$value%'";
		}
		var_dump($sDrawing);

		// 条件を OR で結合し、SQLに組み込む
		$sql .= " AND (" . implode(" OR ", $conditions) . ")";
	}*/
	// 複数の値で検索する場合（$sDrawingが配列）
	if (!empty($sDrawing) && $sDrawing[0] != 0) {  // $sDrawingが空でないかつ0でない場合
		// 配列内の各値をOR条件で結合
		$drawingConditions = [];
		foreach ($sDrawing as $value) {
			$drawingConditions[] = "DRAWING = '$value'";
		}
		// 追加条件をSQLに組み込む
		$sql .= " AND (" . implode(" OR ", $drawingConditions) . ")";
	}
	if ($sSellCharge) {
		$sSellCharge = htmlspecialchars_decode($sSellCharge, ENT_QUOTES);
		$sql .= " AND SELLCHARGE LIKE '%$sSellCharge%'";
	}
	if ($orderBy) {
		$sql .= " ORDER BY $orderBy $orderTo";
	}
	if ($flg) {
		$sql .= " LIMIT " . (($sPage - 1) * PAGE_MAX) . ", " . PAGE_MAX;
	}

	return ($sql);
}



//
//物件管理情報
//
function fnSqlArticleEdit($articleNo)
{
	$sql  = "SELECT ARTICLE, ROOM, KEYPLACE, ADDRESS, ARTICLENOTE, KEYBOX, DRAWING, SELLCHARGE, DEL";
	$sql .= " FROM TBLARTICLE";
	$sql .= " WHERE ARTICLENO = $articleNo";

	return ($sql);
}



//
//物件管理情報更新
//
function fnSqlArticleUpdate($articleNo, $article, $room, $keyPlace, $address, $articleNote, $keyBox, $drawing, $sellCharge, $del)
{
	$sql  = "UPDATE TBLARTICLE";
	$sql .= " SET ARTICLE = '$article'";
	$sql .= ",ROOM = '$room'";
	$sql .= ",KEYPLACE = '$keyPlace'";
	$sql .= ",ADDRESS = '$address'";
	$sql .= ",ARTICLENOTE = '$articleNote'";
	$sql .= ",KEYBOX = '$keyBox'";
	$sql .= ",DRAWING = '$drawing'";
	$sql .= ",SELLCHARGE = '$sellCharge'";
	$sql .= ",UPDT = CURRENT_TIMESTAMP";
	$sql .= ",DEL = '$del'";
	$sql .= " WHERE ARTICLENO = $articleNo";

	return ($sql);
}



//
//物件管理情報登録
//
// function fnSqlArticleInsert($articleNo,  $keyPlace, $article, $address,  $keyBox, $articleNote, $drawing, $sellCharge, $room, $del)
// {
// 	$sql  = "INSERT INTO TBLARTICLE (";
// 	$sql .= " ARTICLENO, ARTICLE, ROOM, KEYPLACE, ADDRESS, ARTICLENOTE, KEYBOX, DUEDT, SELLCHARGE, AREA, YEARS, SELLPRICE, INTERIORPRICE, CONSTTRADER,"
// 		. " CONSTPRICE, CONSTADD, CONSTNOTE, PURCHASEDT, WORKSTARTDT, WORKENDDT, LINEOPENDT, LINECLOSEDT, RECEIVE, HOTWATER, SITEDT, LEAVINGFORM,"
// 		. " LEAVINGDT, MANAGECOMPANY, FLOORPLAN, FORMEROWNER, BROKERCHARGE, BROKERCONTACT, INTERIORCHARGE, CONSTFLG1, CONSTFLG2, CONSTFLG3, CONSTFLG4, INSDT, UPDT, DEL,"
// 		. " DRAWING, LINEOPENCONTACTDT, LINECLOSECONTACTDT, LINECONTACTNOTE, ELECTRICITYCHARGE, GASCHARGE, LIGHTORDER";
// 	$sql .= " ) VALUES ( ";
// 	$sql .= "'$articleNo', '$article', '$room', '$keyPlace', '$address', '$articleNote', '$keyBox', '', '$sellCharge', '', '', '', '', '',"
// 		. " '', '', '', '', '', '', '', '', '', '', '', '',"
// 		. " '', '', '', '', '', '', '', '', '', '', '', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$del',"
// 		. " '$drawing', '', '', '', '', '', '' )";

// 	return ($sql);
// }
function fnSqlArticleInsert($articleNo, $article, $room, $keyPlace, $address, $articleNote, $keyBox, $drawing, $sellCharge, $del)
{
	$sql  = "INSERT INTO TBLARTICLE (";
	$sql .= " ARTICLENO, ARTICLE, ROOM, KEYPLACE, ADDRESS, ARTICLENOTE, KEYBOX, DUEDT, SELLCHARGE, AREA, YEARS, SELLPRICE, INTERIORPRICE, CONSTTRADER,"
		. " CONSTPRICE, CONSTADD, CONSTNOTE, PURCHASEDT, WORKSTARTDT, WORKENDDT, LINEOPENDT, LINECLOSEDT, RECEIVE, HOTWATER, SITEDT, LEAVINGFORM,"
		. " LEAVINGDT, MANAGECOMPANY, FLOORPLAN, FORMEROWNER, BROKERCHARGE, BROKERCONTACT, INTERIORCHARGE, CONSTFLG1, CONSTFLG2, CONSTFLG3, CONSTFLG4, INSDT, UPDT, DEL,"
		. " DRAWING, LINEOPENCONTACTDT, LINECLOSECONTACTDT, LINECONTACTNOTE, ELECTRICITYCHARGE, GASCHARGE, LIGHTORDER";
	$sql .= " ) VALUES ( ";
	$sql .= "'$articleNo', '$article', '$room', '$keyPlace', '$address', '$articleNote', '$keyBox', '', '$sellCharge', '', '', '', '', '',"
		. " '', '', '', '', '', '', '', '', '', '', '', '',"
		. " '', '', '', '', '', '', '', '', '', '', '', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$del',"
		. " '$drawing', '', '', '', '', '', '' )";

	return ($sql);
}


//
//物件管理情報削除
//
function fnSqlArticleDelete($articleNo)
{
	$sql  = "UPDATE TBLARTICLE";
	$sql .= " SET DEL = -1";
	$sql .= ",UPDT = CURRENT_TIMESTAMP";
	$sql .= " WHERE ARTICLENO = '$articleNo'";

	return ($sql);
}

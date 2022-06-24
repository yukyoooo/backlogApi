<?php

$backlogApi = new BacklogApi();
$backlogApi->closeIssue();
$backlogApi->addIssue();


/**
 * 日報をAPIでコントロール
 */
class BacklogApi {
	const API_KEY = '<apiKey>';
	const HOST_NAME = '<host>.backlog.com';
	const PROJECT_ID = '<projectId>';
	const ISSUE_TYPE_ID = '<issueType>';
	const STATUS_NOT_COMPLETE = 1;
	const STATUS_COMPLETE = 4;
	const METHOD_TYPE_GET = 'GET';
	const METHOD_TYPE_PATCH = 'PATCH';
	const METHOD_TYPE_POST = 'POST';
	const PRIORITY_MEDIUM = 3;

	/**
	 * 日報の課題ステータスを完了に変更
	 * @return void
	 */
	function closeIssue() {
		$params = array(
			'apiKey' => self::API_KEY,
			'statusId' => self::STATUS_COMPLETE,
		);
		$issueKey = $this->getIssueKey();
		$url = 'https://'.self::HOST_NAME.'/api/v2/issues/'.$issueKey.'?'.http_build_query($params, '','&');
		$response = $this->getContents($url, self::METHOD_TYPE_PATCH);
		var_dump(date('Y-m-d H:i:s').' 課題更新：'.$response['issueKey'].'　ステータス：'.$response['status']['name']);
	}

	/**
	 * 日報の課題を追加
	 * @return void
	 */
	function addIssue()	{
		$period = $this->getPeriod();
		$title = $period."業務報告";
		$body = $this->makeBody($period);
		$params = array(
			'apiKey' => self::API_KEY,
			'projectId' => self::PROJECT_ID,
			'priorityId' => self::PRIORITY_MEDIUM,
			'issueTypeId' => self::ISSUE_TYPE_ID,
			'summary' => $title,
			'description' => $body,
		);
		$url = 'https://'.self::HOST_NAME.'/api/v2/issues?'.http_build_query($params, '', '&');
		$response = $this->getContents($url, self::METHOD_TYPE_POST);
		var_dump(date('Y-m-d H:i:s').' 課題追加：'.$response['issueKey'].'　ステータス：'.$response['status']['name']);
	}

	/**
	 * 未完了の課題番号を取得
	 * @return mixed
	 */
	private function getIssueKey() {
		$params = array(
			'apiKey' => self::API_KEY,
			'projectId[]' => self::PROJECT_ID,
			'statusId[0]' => self::STATUS_NOT_COMPLETE,
		);
		$url = 'https://' . self::HOST_NAME . '/api/v2/issues?'.http_build_query($params, '','&');
		$response = $this->getContents($url, self::METHOD_TYPE_GET);
		var_dump(date('Y-m-d H:i:s').' 課題取得：'.$response[0]['issueKey'].'　ステータス：'.$response[0]['status']['name']);
		return $response[0]['issueKey'];
	}

	/**
	 * API通信処理
	 * @param string $url
	 * @param string $methodType - GET, PATCH, POST
	 * @return array
	 */
	private function getContents(string $url, string $methodType): array{
		$headers = array('Content-Type:application/x-www-form-urlencoded');
		$context = array(
			'http' => array(
				'method' => $methodType,
				'header' => $headers,
				'ignore_errors' => true,
			)
		);
		$response = file_get_contents($url, false, stream_context_create($context));
		return $this->toJson($response);
	}

	/**
	 * レスポンスを変数で扱えるようにJSONに変換
	 * @param string $response
	 * @return array
	 */
	private function toJson(string $response): array{
		$json = mb_convert_encoding($response, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
		return json_decode($json, true);
	}

	/**
	 * 今日から五日間の期間を取得
	 * @return string - mm/dd〜mm/dd
	 */
	private function getPeriod(): string{
		$dateToday = date("m/d");
		$date5daysLater = date("m/d", strtotime("+5 day"));
		return "【".$dateToday."〜".$date5daysLater."】";
	}

	/**
	 * 課題の概要作成
	 * @param string $period
	 * @return string
	 */
	private function makeBody(string $period): string{
		$br = "\n";
		$text = $period."の業務報告になります。";
		$text .= $br.$br;
		$text .= "## 必須報告項目";
		$text .= $br.$br;
		$text .= "* 勤務時間".$br;
		$text .= "    * 例： 「勤務時間：11:00 - 20:00」".$br;
		$text .= "* 本日実施した作業内容".$br;
		$text .= "* 振り返り";
		$text .= $br.$br;
		$text .= "## 任意報告項目".$br;
		$text .= "* 本日の気付き".$br;
		$text .= "* Keep（tryした結果、うまくいったので明日からも継続したいこと）".$br;
		$text .= "* Problem（今日うまくいかなかったこと。何か問題が発生していること）".$br;
		$text .= "* Try（Problemを解決するために明日からやってみること）".$br;
		$text .= "* その他なんでも。".$br;
		$text .= "    * おいしいお店を見つけたとか".$br;
		$text .= "    * こんなことやってみたいなーとか".$br;
		$text .= "    * 面白い記事みつけたとか".$br;
		$text .= "    * なんか体調悪いとか".$br;
		return $text;
	}
}

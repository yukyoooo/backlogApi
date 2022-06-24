# 日報課題の自動更新＆追加
- 毎週月曜日の9時にcronで`BacklogApi.php`が実行される
- `cronlog.log`にcron実行時のログが出力される

## 参考サイト
- [【PHP】BacklogのAPIを使って課題を取得＆更新してみる](https://qiita.com/ritya/items/b42aaf7b32820e4e31bf)
- [Backlog API で課題を登録する](https://support-ja.backlog.com/hc/ja/articles/360046783973-Backlog-API-%E3%81%A7%E8%AA%B2%E9%A1%8C%E3%82%92%E7%99%BB%E9%8C%B2%E3%81%99%E3%82%8B#PHP)
- [公式backlogApi](https://developer.nulab.com/ja/docs/backlog/)
- [cronログ出力](https://yongjinkim.com/linux%E3%81%A7%E3%82%B3%E3%83%9E%E3%83%B3%E3%83%89%E3%81%AE%E5%AE%9F%E8%A1%8C%E3%83%AD%E3%82%B0%EF%BC%88cron%E3%82%82%EF%BC%89%E3%82%92%E3%83%95%E3%82%A1%E3%82%A4%E3%83%AB%E3%81%AB%E5%87%BA%E5%8A%9B/)

## crontab -e
```
0 9 * * 1 /usr/local/bin/php ./dev/backlogApi/BacklogApi.php >> ./dev/backlogApi/cronlog.log 2>&1
```

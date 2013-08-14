<?php
/*
Copyright (C) 2013 Makoto Mizukami. All rights reserved.

Permission is hereby granted, free of charge, to any person obtaining a
copy of this software and associated documentation files (the "Software"),
to deal in the Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish, distribute, sublicense,
and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.
*/

class UnitwyMsgsLocal extends UnitwyMsgs{
	// Menu & Page titles
	// msg54057 "Home"
	public $msg54057 = "ホーム";
	// msg20828 "Replies"
	public $msg20828 = "リプライ";
	// msg62014 "DM"
	public $msg62014 = "DM";
	// msg57807 "Favourites"
	public $msg57807 = "お気に入り";
	// msg38791 "Friends"
	public $msg38791 = "友達";
	// msg71717 "Followers"
	public $msg71717 = "フォロワー";
	// msg63089 "Lists"
	public $msg63089 = "リスト";
	// msg19279 "Multimedia"
	public $msg19279 = "マルチメディア";
	// msg91602 "Search"
	public $msg91602 = "検索";
	// msg84032 "Trends"
	public $msg84032 = "トレンド";
	// msg55928 "Settings"
	public $msg55928 = "設定";
	// msg48620 "Sessions"
	public $msg48620 = "セッション";
	// msg57858 "Logout"
	public $msg57858 = "ログアウト";



	// visible features
	// dm
	// msg86601 "Compose DM"
	public $msg86601 = "DM の作成";
	// msg48554 "DM Sent"
	public $msg48554 = "DM 送信済み";
	// msg93026 "DM Inbox"
	public $msg93026 = "DM 受信箱";

	// msg40339 "Compose"
	public $msg40339 = "作成";
	// msg24993 "Inbox"
	public $msg24993 = "受信箱";
	// msg32082 "Sent"
	public $msg32082 = "送信済み";

	// msg93268 "Sending DMs to this user is not permitted. You can send DMs only to users following you."
	public $msg93268 = "このユーザーへの DM の送信は許可されていません。 DM はあなたをフォローしているユーザー宛にのみ送信できます。";

	// msg62099 "Are you sure of the deletion?"
	public $msg62099 = "本当に削除してよろしいですか?";

	// lists
	// msg66568 "Belongings"
	public $msg66568 = "所有しているリスト";
	// msg58309 "Subscriptions"
	public $msg58309 = "購読しているリスト";
	// msg94757 "Memberships"
	public $msg94757 = "追加されているリスト";

	// msg81973 "Create a list"
	public $msg81973 = "リストの作成";
	// msg30397 "Create"
	public $msg30397 = "作成";

	// msg47066 "No lists to display"
	public $msg47066 = "表示するリストがありません。";

	// search
	// msg31476 "Search"
	public $msg31476 = "検索";

	// settings
	// msg84505 "Cleared Credentials"
	public $msg84505 = "認証情報の消去";
	// msg37594 "Cleared you credentials from the server completely."
	public $msg37594 = "認証情報をサーバーから完全に消去しました。";
	// msg38575 "Trying to clear all of your credentials from the server. Are you sure?"
	public $msg38575 = "認証情報をサーバーから消去しようとしています。よろしいですか?";
	// msg47951 "Back"
	public $msg47951 = "戻る";

	// msg58718 "no"
	public $msg58718 = "いいえ";
	// msg74569 "yes"
	public $msg74569 = "はい";

	// msg60763 "Language"
	public $msg60763 = "言語";

	// msg37212 "Preferences"
	public $msg37212 = "設定";
	// msg76645 "Skin"
	public $msg76645 = "スキン";
	// msg46874 "Use Google Mobile Proxy"
	public $msg46874 = "Google Mobile Proxy を使用する";
	// msg88525 "Show tweeted time always in that of day"
	public $msg88525 = "ツイートされた日時を時刻で表示する";
	// msg33119 "Do not embed multimedia contents to timelines"
	public $msg33119 = "マルチメディアコンテンツをタイムラインに埋めこまない";
	// msg35387 "Intervals of automatic reloads (seconds)"
	public $msg35387 = "自動更新の間隔 (秒)";
	// msg27762 "Set 0 to disable the function."
	public $msg27762 = "機能を停止するには 0 を指定します。";
	// msg32196 "Interval of queries for automatic notifications (seconds)"
	public $msg32196 = "自動通知の問い合わせ間隔 (秒)";
	// msg69008 "Set 0 to disable this function."
	public $msg69008 = "この機能を停止するには 0 を指定します。";
	// msg77127 "Timezone"
	public $msg77127 = "タイムゾーン";
	// msg66749 "See all available timzones"
	public $msg66749 = "利用可能なタイムゾーンをすべて表示する";
	// msg39021 "Reset all settings"
	public $msg39021 = "すべての設定を初期化する";

	// msg23004 "Close other sessions"
	public $msg23004 = "他のセッションを閉じる";
	// msg99134 "Close other sessions opened for this user"
	public $msg99134 = "他の開いたセッションを閉じる";

	// msg24652 "Profile"
	public $msg24652 = "プロフィール";
	// msg54428 "Name"
	public $msg54428 = "名前";
	// msg21443 "URL"
	public $msg21443 = "URL";
	// msg16052 "Location"
	public $msg16052 = "場所";
	// msg31963 "Description"
	public $msg31963 = "説明";

	// msg45845 "Picture"
	public $msg45845 = "写真";
	// msg66219 "New picture"
	public $msg66219 = "新しい写真";

	// msg93827 "Connection to Facebook"
	public $msg93827 = "Facebook との連携";
	// msg66752 "Connect to your Facebook account"
	public $msg66752 = "Facebook アカウントに接続する";
	// msg67196 "Connected to %s"
	public $msg67196 = "%s と接続済み";
	// msg89432 "this user"
	public $msg89432 = "このユーザー";
	// msg75701 "Keep it connected by saving credentials to the server"
	public $msg75701 = "サーバーに認証情報を保存して接続を保持する";
	// msg48127 "Your Facebook credentials are saved to the server."
	public $msg48127 = "Facebook の認証情報は既にサーバーに保存されています。";
	// msg77087 "Disconnect & wipe out all the credentials"
	public $msg77087 = "切断と認証情報の削除";

	// msg97036 "Keep your Twitter OAuth credentials in the server"
	public $msg97036 = "Twitter OAuth 認証情報をサーバーに保存する";
	// msg26068 "You have already saved your Twitter credentials to the server and you can update them here."
	public $msg26068 = "Twitter の認証情報は既にサーバーに保存されています。ここでそれらを更新することができます。";
	// msg55886 "Clear all of your credentials from the server"
	public $msg55886 = "すべての認証情報をサーバーから消去する";
	// msg30823 "Password"
	public $msg30823 = "パスワード";
	// msg34199 "This password is highly recommended to be different from one for Twitter!"
	public $msg34199 = "Twitter のパスワードとは違うものを使用することが強く推奨されます!";

	// msg67168 "Save"
	public $msg67168 = "保存";

	// trends
	// msg77884 "Choose"
	public $msg77884 = "選択";



	// hidden features
	// addtolist
	// msg32820 "Add user to list"
	public $msg32820 = "リストへのユーザーの追加";
	// msg67751 "Add the user to a list"
	public $msg67751 = "ユーザーをリストに追加します。";
	// msg86983 "Choose a list to add from the below."
	public $msg86983 = "追加先のリストを選択してください。";
	// msg31114 "There is no list to display."
	public $msg31114 = "表示するリストがありません。";
	// msg33034 "Please create at least one."
	public $msg33034 = "1つ以上のリストを作成してください。";

	// block
	// msg72755 "Trying to block the user"
	public $msg72755 = "ユーザーをブロックしようとしています";

	// delete
	// msg23382 "Are you sure of the deletion?"
	public $msg23382 = "本当に削除してよろしいですか?";

	// facebook
	// msg13704 "Canceled."
	public $msg13704 = "中断しました。";
	// msg38735 "Unknown error."
	public $msg38735 = "不明なエラーが発生しました。";

	// hash
	// msg81446 "Hashtag"
	public $msg81446 = "ハッシュタグ";

	// incoming
	// msg36971 "Pended Follower Requests"
	public $msg36971 = "承認待ちフォローリクエスト";
	// msg56138 "Note: Currently approving/declining requests via third party clients is restricted by Twitter."
	public $msg56138 = "注: 現在 Twitter 側の規制により、サードパーティークライアントからではリクエストを承諾・拒否できません。";

	// list
	// msg78625 "List"
	public $msg78625 = "リスト";
	// msg23469 "Edit"
	public $msg23469 = "編集";
	// msg85505 "Unsubscribe"
	public $msg85505 = "購読解除";
	// msg41051 "Subscribe"
	public $msg41051 = "購読";
	// (unit)
	// msg52540 "member"
	public $msg52540 = "人のメンバー";
	// msg49853 "subscriber"
	public $msg49853 = "人の購読者";

	// msg74462 "Members"
	public $msg74462 = "メンバー";
	// msg62270 "Add a user to this list"
	public $msg62270 = "ユーザーをリストに追加する";
	// msg47706 "Add"
	public $msg47706 = "追加";

	// msg47526 "Subscribers"
	public $msg47526 = "購読者";

	// msg84818 "Edit"
	public $msg84818 = "編集";
	// msg93977 "Update"
	public $msg93977 = "更新";

	// msg27324 "Update"
	public $msg27324 = "更新";
	// msg98797 "Delete"
	public $msg98797 = "削除";

	// media
	// msg28139 "Uploading succeeded."
	public $msg28139 = "アップロードに成功しました。";
	// msg52215 "Tweeted the media successfully."
	public $msg52215 = "メディアのツイートに成功しました。";

	// post
	// msg51899 "Post Tweet"
	public $msg51899 = "ツイートの投稿";

	// quote
	// msg52828 "Quote"
	public $msg52828 = "引用";

	// rmls
	// msg23117 "Are you sure of the deletion?"
	public $msg23117 = "本当に削除してよろしいですか?";

	// spam
	// msg96131 "Trying to report the user as a spammer"
	public $msg96131 = "ユーザーをスパマーとして報告しようといています";

	// status
	// msg33898 "Status"
	public $msg33898 = "ステータス";
	// msg81829 "Reply To"
	public $msg81829 = "返信";
	// msg77791 "Reply from here"
	public $msg77791 = "ここから返信します";
	// msg75804 "Reply with multimedia contents"
	public $msg75804 = "マルチメディアコンテンツと共に返信する";
	// msg52719 "Conversation"
	public $msg52719 = "会話";

	// unblock
	// msg83029 "Trying to unblock the user"
	public $msg83029 = "ユーザーのブロックを解除しようとしています";

	// user
	// msg84393 "User"
	public $msg84393 = "ユーザー";



	// functions
	// accountLogin
	// msg49493 "For users saved their Twitter OAuth credentials to the server"
	public $msg49493 = "Twitter の OAuth 認証情報をサーバーに保存しているユーザーはここからログインできます";
	// msg92977 "Username"
	public $msg92977 = "ユーザー名";
	// msg84133 "Password"
	public $msg84133 = "パスワード";
	// msg40944 "Login"
	public $msg40944 = "ログイン";
	// msg51113 "Username or password is invalid."
	public $msg51113 = "無効なユーザー名とパスワードが入力されました。";

	// confirmation_lock
	// msg33209 "Are you sure?"
	public $msg33209 = "本当によろしいですか?";
	// msg82562 "Sure"
	public $msg82562 = "はい";
	// msg54650 "Back"
	public $msg54650 = "戻る";

	// dmTimeline
	// msg66508 "No DMs to display."
	public $msg66508 = "表示する DM はありません。";

	// formatDatetime
	// msg27380 "sec"
	public $msg27380 = "秒";
	// msg39136 "min"
	public $msg39136 = "分";
	// msg89052 "hour"
	public $msg89052 = "時間";
	// msg49898 " ago"
	public $msg49898 = "前";

	// format_unit
	// postfix of the plurals
	// msg12117 "s"
	public $msg12117 = "";

	// langChooser
	// msg43948 "Change"
	public $msg43948 = "変更";

	// listList
	// msg47125 "Private"
	public $msg47125 = "非公開";
	// msg33817 "Edit"
	public $msg33817 = "編集";

	// listInfoForm
	// msg51241 "Public"
	public $msg51241 = "公開";
	// msg64051 "Private"
	public $msg64051 = "非公開";
	// msg59772 "Name"
	public $msg59772 = "名前";
	// msg69849 "Mode"
	public $msg69849 = "モード";
	// msg97956 "Description (optional)"
	public $msg97956 = "説明 (任意)";

	// loginForm
	// msg63422 "Language"
	public $msg63422 = "言語";
	// msg25369 "Start a new session"
	public $msg25369 = "新しいセッションを開始する";

	// msg57003 "Unitwy is"
	public $msg57003 = "Unitwy とは";
	// msg38234 "a Web-based Universal Twitter client for many devices, including Desktops and Mobiles."
	public $msg38234 = "Web ベースのユニバーサルなクライアントで、デスクトップやモバイルを含め様々な環境で使えます。";
	// msg20406 "aiming to be a powerful alternation of the official Twitter web client."
	public $msg20406 = "Twitter の Web クライアントの強力な置き換えとなることを目指しています。";
	// msg16618 "multilingual."
	public $msg16618 = "多言語化されています。";
	// msg12527 "supporting multisession which enables you to use plural Twitter accounts simultaneously."
	public $msg12527 = "マルチセッションに対応しており、複数の Twitter アカウントを同時に使えます。";
	// msg15347 "free to use!"
	public $msg15347 = "無料で使えます!";

	// main
	// msg62619 "Feature not found."
	public $msg62619 = "機能がみつかりませんでした。";

	// mediaForm
	// msg79889 "Post multimedia contents"
	public $msg79889 = "マルチメディアコンテンツを送信する";
	// msg98280 "File"
	public $msg98280 = "ファイル";
	// msg99713 "Message (optional)"
	public $msg99713 = "メッセージ (任意)";
	// msg80077 "Service"
	public $msg80077 = "サービス";
	// msg60380 "Upload only (without tweeting it)"
	public $msg60380 = "アップロードのみ (ツイートしない)";
	// msg54277 "Post"
	public $msg54277 = "送信";

	// MediaPost
	// msg26017 "Unknown"
	public $msg26017 = "不明";
	// msg44509 "Upload failed"
	public $msg44509 = "アップロードに失敗しました。";

	// output_error
	// msg12748 "Error"
	public $msg12748 = "エラー";

	// pagination_label
	// msg19570 "Previous"
	public $msg19570 = "前";
	// msg91205 "Next"
	public $msg91205 = "次";

	// searchTimeline
	// msg42227 "Not found."
	public $msg42227 = "みつかりませんでした。";

	// sessionChooser
	// msg25595 "Resume already logged-in session"
	public $msg25595 = "ログイン済みのセッションを再開する";
	// msg97367 "Go"
	public $msg97367 = "Go";
	// msg99990 "Close all sessions"
	public $msg99990 = "すべてのセッションを閉じる";

	// statusBox
	// msg57999 "%sretweeted%s by"
	public $msg57999 = "%sリツイート%s元";
	// msg29289 "original tweet"
	public $msg29289 = "元のツイート";
	// msg40771 "retweeted times"
	public $msg40771 = "リツイートされた回数";
	// msg83527 "from %s"
	public $msg83527 = "%s から";

	// statusForm / dmForm
	// msg43620 "To"
	public $msg43620 = "宛先";
	// msg22815 "Post"
	public $msg22815 = "送信";
	// msg33103 "Clear"
	public $msg33103 = "クリア";
	// msg30883 "Add geotag"
	public $msg30883 = "ジオタグを追加";
	// msg70982 "Locating..."
	public $msg70982 = "測位中...";
	// msg87768 "Geolocation is not available..."
	public $msg87768 = "Geolocation を利用できません。";

	// statusesTimeline
	// msg58524 "No tweets to display."
	public $msg58524 = "表示するツイートはありません。";

	// twitterLogin
	// msg61921 "Login with Twitter"
	public $msg61921 = "Twitter 経由でログイン";

	// twitter_process
	// msg59543 "cURL Error"
	public $msg59543 = "cURL エラー";
	// msg26577 "Unknown error"
	public $msg26577 = "不明なエラー";
	// msg66113 "HTTP Error"
	public $msg66113 = "HTTP エラー";

	// user_authenticate_user
	// msg27713 "Login"
	public $msg27713 = "ログイン";

	// userHeader
	// msg60890 "Homepage"
	public $msg60890 = "ホームページ";
	// msg73197 "Location"
	public $msg73197 = "場所";
	// msg66761 "Veryfied"
	public $msg66761 = "認証済み";
	// msg84551 "Protected"
	public $msg84551 = "保護";
	// msg22805 "Joined"
	public $msg22805 = "加入日";
	// msg51248 "tweet"
	public $msg51248 = "ツイート";
	// msg36831 "day"
	public $msg36831 = "日";
	// msg67761 "Following you!"
	public $msg67761 = "あなたをフォローしています!";
	// msg76010 "Friends"
	public $msg76010 = "友達";
	// msg45529 "Followers"
	public $msg45529 = "フォロワー";
	// msg98629 "Favourites"
	public $msg98629 = "お気に入り";
	// msg79736 "Lists Followed-by"
	public $msg79736 = "ユーザーを追加しているリスト";
	// msg35389 "Check Pending Follower Requests"
	public $msg35389 = "承認待ちフォローリクエストを確認";
	// msg41827 "Send Direct Message"
	public $msg41827 = "ダイレクトメッセージを送信";
	// msg44099 "Unfollow"
	public $msg44099 = "フォロー解除";
	// msg30205 "Filter Retweets"
	public $msg30205 = "リツイートをフィルタする";
	// msg46065 "Receive Retweets"
	public $msg46065 = "リツイートを受け取る";
	// msg23918 "Follow"
	public $msg23918 = "フォロー";
	// msg28926 "Add to a List"
	public $msg28926 = "リストに追加";
	// msg13305 "Unblock"
	public $msg13305 = "ブロック解除";
	// msg22781 "Block"
	public $msg22781 = "ブロック";
	// msg14790 "Report Spam"
	public $msg14790 = "スパムとして報告";

	// userList
	// msg31439 "No users to display."
	public $msg31439 = "表示するユーザーはありません。";
	// msg59371 "Never Tweeted"
	public $msg59371 = "ツイートなし";
	// msg13760 "Last Tweet"
	public $msg13760 = "最後のツイート";
	// msg69416 "Protected User"
	public $msg69416 = "保護されたユーザー";
}

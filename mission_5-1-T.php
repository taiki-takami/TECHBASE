<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5-1-T</title>
    </head>
    <body>
<?php
//------データベースへの接続----------------------------------------------------------------------------------
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//------------------------------------------------------------------------
//------テーブル作成-------------------------------------------------------------------------------
	$sql = "CREATE TABLE IF NOT EXISTS mission5_T"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	."str TEXT,"
	."pass TEXT,"
	. "time TEXT"
	.");";
	$stmt = $pdo->query($sql);
//---------------------------------------------------------------------------------------------

//----準備--------------------------------------------------------------
        $name = $_POST["name"];           
        $str = $_POST["str"];
        $pass = $_POST["pass"];
        $time = date("Y/m/d H:i:s");//時間に使用
        $hash = password_hash($pass, PASSWORD_DEFAULT);
//-------------------------------------------------------------------------

//---------登録-----------------------------------------------------------
if(!empty($_POST["name"]) && !empty($_POST["str"]))
{
    if(password_verify("pass", $hash))
    {
        if($_POST["overwrite"]=="")
        {
                    //データを入力-----------------------------------------------------------------
        //prepare:文を実行する準備を行い、文オブジェクトを返す↓
        $sql = $pdo -> prepare("INSERT INTO mission5_T (name, str, pass, time) 
                                 VALUES (:name, :str, :pass, :time)");
        /*
        bindValue：値をバイントする
        bindParam：変数をバイントする
        バインド：PDOでは、基本的に変数を直接、扱えません。そこで、変数を「：」で
        始まるものに結びつけ(関連付け)ているのです。
        */
	    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    	$sql -> bindParam(':str', $str, PDO::PARAM_STR);
    	$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
    	$sql -> bindParam(':time', $time, PDO::PARAM_STR);
    	$sql -> execute();
    	//bindParamの引数名（:name など）はテーブルのカラム名に併せるとミスが少なくなります。
    	//最適なものを適宜決めよう。
        }
        
        else if($_POST["overwrite"]!=="")
        {
            echo "OK";
            $id=$_POST["overwrite"];
            $sql='UPDATE mission5_T SET name=:name,str=:str,pass=:pass,
                               time=:time WHERE id=:id';
            $stmt= $pdo->prepare($sql);
            $stmt->bindParam(':name',$name,PDO::PARAM_STR);
            $stmt->bindParam(':str',$str,PDO::PARAM_STR);
            $stmt->bindParam(':pass',$pass,PDO::PARAM_STR);
            $stmt->bindParam(':time',$time,PDO::PARAM_STR);
            $stmt->bindParam(':id',$id,PDO::PARAM_STR);
            $stmt->execute();
        }
        
        
    }
}
//----------------------------------------------------------------------------------

//----削除----------------------------------------------------------------------
if(!empty($_POST["delete"]))
{
    if(password_verify("pass", $hash))
    {
            $delete_num=$_POST["delete"];
            $sql='delete from mission5_T where id=:id';
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':id',$delete_num,PDO::PARAM_INT);
            $stmt->execute();
    }
}
//-------------------------------------------------------------------

//-------編集---------------------------------------------------
if(!empty($_POST["edit"]))
{
     if(password_verify("pass", $hash))
     {
        $id=$_POST["edit"];
        $sql='SELECT * FROM mission5_T WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id,PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach($results as $row)
        {
        //編集する値の取得
            $num_e=$row['id'];
            $name_e=$row['name'];
            $str_e=$row['str'];
            $pass_e=$row['pass'];
        }
        
        /*
        $delete_edit_number=$_POST["edit"];
        $sql='delete from mission5_T where id=:id';
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':id',$delete_edit_number,PDO::PARAM_INT);
        $stmt->execute();
        */
     }
}
        /*
       if($_POST["overwrite"]!=="")
        {
            $id=$_POST["overwrite"];
            $sql='UPDATE mission5_T SET name=:name,str=:str,pass=:pass,
                               time=:time WHERE id=:id';
            $stmt= $pdo->prepare($sql);
            $stmt->bindParam(':name',$name_e,PDO::PARAM_STR);
            $stmt->bindParam(':str',$str_e,PDO::PARAM_STR);
            $stmt->bindParam(':pass',$pass_e,PDO::PARAM_STR);
            $stmt->bindParam(':time',$time,PDO::PARAM_STR);
            $stmt->bindParam(':id',$num_e,PDO::PARAM_STR);
            $stmt->execute();
        }
        */


  

?>
<!---フォーム・まとめ----------------------------------------------------------------->
         <form method="POST">
            <input type="text" name="name" placeholder="名前" 
            value="<?php if(!empty($name_e)){echo $name_e;} ?>"><br>
            <input type="text" name="str" placeholder="コメント" 
            value="<?php if(!empty($str_e)){echo $str_e;} ?>"><br>
            <input type="number" type="number" name="overwrite"
            value="<?php if(!empty($_POST['edit'])){echo $num_e;} ?>">
             <input type="text" name="pass" placeholder="パスワード">
            <input type="submit" value="送信">
            <p>
            </p>
            
        </form>
        <form method="POST">
            <input type="number" name="delete" placeholder="削除したい行数"><br>
            <input type="text" name="pass" placeholder="パスワード">
            <input type="submit" value="送信">
             <p>
            </p>
        </form>
        <form method="POST">
            <input type="number" name="edit" placeholder="編集したい行数"><br>
           <input type="text" name="pass" placeholder="パスワード">
            <input type="submit" value="送信" name="edit_submit">
        </form>
<!----------------------------------------------------------------------------->
</body>
<?php
    //掲示板表示
    $sql ='SELECT * FROM mission5_T';
    $stmt = $pdo->query($sql);
    $results = $stmt -> fetchAll();
    foreach($results as $row)
    {
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['str'].',';
        echo $row['pass'].',';
        echo $row['time'].'<br>';
        
        echo"<hr>";
    }
?>
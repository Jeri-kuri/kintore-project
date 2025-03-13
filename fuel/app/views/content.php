<html>
    <body>
        <h1>
            配列情報の表示    
        </h1>

        <table>
        <?php foreach($members as $member): ?>    
        <tr>    
                    <td><?php echo $member['name']; ?></td>
                    <td><?php echo $member['age']; ?></td>
                <?php endforeach;?>
            </tr>
        </table>
    </body>
</html>
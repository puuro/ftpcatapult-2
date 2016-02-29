# fictional-garbanzo
FTP sync tool written in PHP for UNIX/LINUX/MAC. Only thing that is OS specific is some shell commands. Tip to Windows users: Virtualbox.<br>
May be useful if you have only FTP connection to server and want to edit the files and if you don't like doing development in a FTP client and if you don't want to keep track which files you have edited and if you don't want to send a whole directory to server after you have made a small change. So this tool enables you to edit files locally with editor you like and deploy them to server easily and fast and it enables you to do this from multiple computers and it enables your friends, too, to sync your files and help you.<br>

INSTALLATION


Put pultd.php and conf.php files in a folder. Set variables in conf.php. Local path is the directory where you work with your code, e.g. "../work/". Remote path is path in server from your FTP home directory e.g. "/htdocs/".<br>
If you don't have the project in your hard disk yet, save it now to the directory that you set in conf.php. You can also leave it empty if you wish to only pull single files from the server.<br>
Run php pultd.php init.<br>
This creates directories in tool's folder.<br>
Run php pultd.php push list.<br>
This creates a list of files in your project directory and sends it to server. Possible other contributors to the same server don't need to do this command.<br>

SOME EXPLANATION

"image/" directory has an up-to-date image of your server contents, that's why it's called image. When you push files to server the tool compares your project directory and image directory and sees which files have changed. So comparison happens offline and is fast.<br>
"files/" directory has some files that are used in determining which files should be pulled. They are also updated when pushing.<br>
Tool maintains a list of all files and when they have changed. The file is located in the server and is downloaded every time you push or pull. It also keeps in memory when you have last pushed or pulled. When you pull it makes comparisons between those times to determine which files have changed and should be pulled.<br>
Multiple users can make changes to the files at the server. Users can pull the changes each of them has made.<br>
Files are never deleted in server but they may be overwritten.<br>
Files are never deleted locally either but they may be overwritten.<br>

COMMANDS

Run php pultd.php [ARGUMENT] [ARGUMENT] ...<br>
Run php pultd.php for a list of commands.<br>
init: Create directories and copy content from project folder to image folder.<br>
push: Send to server files that have changed or are new.<br>
push anyway: Push anyway, even though there may be conflicts(Files in server have changed since your last pull/push)<br>
push list: Create a list of files and send it to server.<br> 
pull: Pull files from server that have changed since last pull. They are downloaded to image folder not project folder.<br>
pull brave: Pull files from server that have changed since last pull. They are downloaded to image folder and project folder.<br>
pull local: Copy files from image folder to project folder.<br>
pull force: Pull everything from server.<br>
pull file [FILE]: Pull a single file from server. You can edit and push single files, too. You don't need to have a whole project on your hard disk.<br>
i'm_up_to_date: If you know you're files are up to date you may use this command so files are not pulled that have changed before current time.<br>

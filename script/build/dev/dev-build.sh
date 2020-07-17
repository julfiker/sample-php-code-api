while true; do
    read -p "Do you wish to make a dev build from your current working source files? (y/n): " yn
    case $yn in
        [Yy]* ) break;;
        [Nn]* ) exit;;
        * ) echo "Please answer yes or no.";;
    esac
done
echo "******************************************************";
echo "Making a new build.. please wait for a while..";
echo "******************************************************";
echo "";
echo "Syncing files.."
rsync -rvz --progress -e "ssh -i \"$HOME/.ssh/keys/spoly-dev.pem\"" .env ubuntu@ec2-52-74-167-106.ap-southeast-1.compute.amazonaws.com:/var/www/spoly-api
cd ../../../src/
rsync -rvz --progress -e "ssh -i \"$HOME/.ssh/keys/spoly-dev.pem\"" . ubuntu@ec2-52-74-167-106.ap-southeast-1.compute.amazonaws.com:/var/www/spoly-api --exclude={.env,.git,vendor,storage/app/*,storage/framework/cache/*,storage/framework/sessions/*,storage/framework/views/*,storage/logs/*}

echo "Setting folder permission"
ssh -i ~/.ssh/keys/spoly-dev.pem ubuntu@ec2-52-74-167-106.ap-southeast-1.compute.amazonaws.com -t 'sudo chown www-data:www-data /var/www/spoly-api/storage -R'
ssh -i ~/.ssh/keys/spoly-dev.pem ubuntu@ec2-52-74-167-106.ap-southeast-1.compute.amazonaws.com -t 'sudo chmod ug+rw -R /var/www/spoly-api/storage'
ssh -i ~/.ssh/keys/spoly-dev.pem ubuntu@ec2-52-74-167-106.ap-southeast-1.compute.amazonaws.com -t 'sudo chown www-data:www-data /var/www/spoly-api/bootstrap/cache -R'
ssh -i ~/.ssh/keys/spoly-dev.pem ubuntu@ec2-52-74-167-106.ap-southeast-1.compute.amazonaws.com -t 'sudo chmod ug+rw -R /var/www/spoly-api/bootstrap/cache'

echo "Installing vendors and running migrations.."
ssh -i ~/.ssh/keys/spoly-dev.pem ubuntu@ec2-52-74-167xcxcxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx    vb-106.ap-southeast-1.compute.amazonaws.com 'bash -s' < ../script/build/dev/run-on-remote.sh
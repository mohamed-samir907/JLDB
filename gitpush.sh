# if Linux : chmod +x gitpush.sh
# ./gitpush.sh

git add .

echo 'Enter the commit message:'
read commitMessage

git commit -m "$commitMessage"

echo 'Enter the name of the branch:'
read branch

git push origin $branch

read


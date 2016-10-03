## WooCommmerce Box Packer

This is for use inside shipping extensions that require the box packer. It can be included as a subtree.

See: https://hpc.uni.lu/blog/2014/understanding-git-subtree/

### Basic Sub-Tree Setup Instructions

git remote add -f box-packer https://github.com/woothemes/box-packer.git
git fetch box-packer
git subtree add --prefix includes/box-packer --squash box-packer/master

### Updating the Sub-Tree

git fetch box-packer
git subtree pull --prefix includes/box-packer box-packer master --squash

### Seeing Diff

 git diff box-packer/master master:includes/box-packer

### Pushing to the Sub-tree

git subtree push --prefix includes/box-packer box-packer master
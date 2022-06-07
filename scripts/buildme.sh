#!/bin/bash

# Make sure this script has +x 

echo "installing bootstrap"
cp ./node_modules/bootstrap/dist/js/*.min.* ./site/js/
cp ./node_modules/bootstrap/dist/css/*.min.* ./site/css/

echo "installing bootstrap-datepicker"
cp ./node_modules/bootstrap-datepicker/dist/js/*.min.* ./site/js/
cp ./node_modules/bootstrap-datepicker/dist/css/*.standalone.min.* ./site/css/
cp ./node_modules/bootstrap-datepicker/dist/css/*.standalone.*.map ./site/css/

# echo "installing icons"
# cp -r ./node_modules/bootstrap-icons/icons ./site/
# cp -r ./node_modules/bootstrap-icons/font/ ./site/

#echo "installing jQuery"
cp ./node_modules/jquery/dist/jquery.min.* ./site/js/

echo "install popperjs"
cp -r ./node_modules/@popperjs/core/dist/umd/popper.min.* ./site/js/
# DwD - Magento Extension - cPanel Connector

## About the extension:

Sell your cPanel WHM hosting packages through Magento.

## Features:

DwD - Cpanel Connector allows you to associate Magento products with Cpanel packages, that way you will be able to sell hosting packages through Magento and the hosts will be created automatically.

## How this extension will help you?

Let's say for example you have to import a lot of images to Magento. In order to import the images using the default import feature, first you will need to upload everything to the /media/import/ folder, that will allow you to use the CSV file to assign the images to the products. The common issue is how to upload the images to the server, for that matter you usually need a FTP or SFTP client, that's an unfriendly process for unexperienced administrators. Here is were the extension will help, administrators can use the AWS S3 clients / process to upload everything to the buckets and then import the buckets contents to Magento. With this extension administrators will be able to change the buckets and folders configuration before run every import, that will allow them to import several buckets to different folders. Note: if a bucket import fails, administrators will be able to run the import again and the download process will start on the error point.

## How it works?

In order to make the extension work you need a Cpanel Account with reseller privileges. When you start the import process from our extension System Configuration the S3 Connector will download all the files from that bucket to the specified Magento folder.

## Use it in simple steps:

- Go to System / Configuration / DwD Extensions / S3 Connector
- Configure your key and secret key
- Specify the source bucket in S3
- Specify the destination folder
- Save the configuration
- Run the import
- If you need to import a different bucket to a different folder just modify the information, save the configuration and run the import again. You can do this as many times you like or need.
- Enjoy!

## Supported versions

Magento 1.8 to 1.9.x.

##Support

For support, contact us at <a href="mailto:info@dwdeveloper.com">info@dwdeveloper.com</a>

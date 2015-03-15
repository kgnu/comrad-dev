#
# Cookbook Name:: comrad
# Recipe:: default
#
# Copyright 2012, YOUR_COMPANY_NAME
#
# All rights reserved - Do Not Redistribute
#

package "phpmyadmin"

execute "disable-default-site" do
  command "a2dissite default"
end

# TODO: Find out if there's any way to do this without user intervention
#       If not, implement it with subclass of SchemaShell that sets $this->interactive to false so that defaults are used
#       We're tied to MySQL until we implement this
#       http://stackoverflow.com/questions/10566180/is-there-any-way-to-run-cake-schema-create-with-chef
#       http://cakephp.lighthouseapp.com/projects/42648/tickets/1996-cake-schema-create-without-user-intervention
#
# execute "create-comrad-db" do
#   command "mysql -u root -p#{node[:mysql][:server_root_password]} -e 'CREATE DATABASE comrad'"
# end
#
# execute "load-comrad-db-schema" do
#   command "/vagrant/cakephp/lib/Cake/Console/cake schema create -f -app /vagrant/cakephp/app"
# end

execute "load-comrad-data" do
  command "mysql -u root -p#{node[:mysql][:server_root_password]} < /vagrant/db.sql"
end

web_app "comrad" do
  application_name "comrad"
  docroot "/vagrant"
end

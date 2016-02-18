# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure('2') do |config|

  # Configure virtualbox to allow $ram-MB memory and symlinks
  config.vm.provider :virtualbox do |vb|
    vb.customize ['modifyvm', :id, '--memory', 512]
    vb.customize ['setextradata', :id, 'VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root', '1']
  end

  # Use Ubuntu LTS 14.04 x64
  config.vm.box = 'trusty64_121sys'
  config.vm.box_url = 'https://cloud-images.ubuntu.com/vagrant/trusty/current/trusty-server-cloudimg-amd64-vagrant-disk1.box'

  # Set up network for HTTP/HTTPS and MySQL
  config.vm.network :forwarded_port, guest: 80, host: 8082
  config.vm.network :forwarded_port, guest: 443, host: 8445
  config.vm.network :forwarded_port, guest: 3306, host: 3308
  config.vm.network :forwarded_port, guest: 1080, host: 1082
  config.vm.network :forwarded_port, guest: 11211, host: 11213

  config.vm.network :private_network, ip: '192.168.111.224'

  config.vm.synced_folder '.', '/vagrant', owner: 'www-data', group: 'www-data', :mount_options => ['dmode=777,fmode=777']

  config.vm.provision "shell", path: "vagrant/provision.sh"

end
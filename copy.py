import paramiko
import os
import stat
import fnmatch

# Учетные данные и пути
hostname = 'a217442.ftp.mchost.ru'
port = 22
username = 'a217442_kami194'
password = 'Q1yI114bZ7'
remote_path = '/home/httpd/vhosts/kamin-sklad.ru/httpdocs/'
local_path = 'C:/Users/evsch/PhpstormProjects/kamin-sklad/'
excluded_dirs = ['upload', 'uploads', '_old', '301']
excluded_files = ['*.xml']

def sftp_copy(sftp, remote_dir, local_dir):
    try:
        os.makedirs(local_dir, exist_ok=True)
        for item in sftp.listdir_attr(remote_dir):
            remote_item_path = remote_dir + item.filename
            local_item_path = os.path.join(local_dir, item.filename)

            # Проверка, является ли элемент директорией
            if stat.S_ISDIR(item.st_mode):
                if item.filename not in excluded_dirs:
                    print(f'Entering directory: {remote_item_path}')
                    sftp_copy(sftp, remote_item_path + '/', local_item_path)
                else:
                    print(f'Skipping directory: {remote_item_path}')
            else:
                # Проверка, нужно ли исключить файл
                if not any(fnmatch.fnmatch(item.filename, pattern) for pattern in excluded_files):
                    print(f'Copying file: {remote_item_path} to {local_item_path}')
                    sftp.get(remote_item_path, local_item_path)
                else:
                    print(f'Skipping file: {remote_item_path}')
    except Exception as e:
        print(f'Error while copying: {e}')

def main():
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(hostname, port, username, password)
        sftp = ssh.open_sftp()

        print(f'Starting copy from {remote_path} to {local_path}')

        sftp_copy(sftp, remote_path, local_path)

        print('Copying completed')
    except Exception as e:
        print(f'Error: {e}')
    finally:
        sftp.close()
        ssh.close()

if __name__ == "__main__":
    main()

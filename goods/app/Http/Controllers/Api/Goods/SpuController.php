<?php

namespace App\Http\Controllers\Api\Goods;

use App\Http\Controllers\Api\Controller;
use App\Model\Goods\Category;
use App\Model\Goods\Collection;
use App\Model\Goods\Config;
use App\Model\Goods\Pim;
use App\Model\Goods\ProductCat;
use App\Model\Goods\Spec;
use App\Model\Goods\SpuDetail;
use App\Service\Goods\AdService;
use App\Service\Goods\CategoryService;
use App\Service\Goods\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Model\Goods\Spu;
use App\Model\Goods\Sku;
use App\Model\Goods\SpuToSku;
use App\Model\Goods\RedisModel;
use App\Model\Goods\ProductHelp;
use App\Model\Goods\WechatHelp;
use Illuminate\Support\Facades\Validator;


class SpuController extends Controller
{
    protected $_model = Spu::class;

    public function dir($dir){
//        $dir = 'D:\dlc商品图';
        if(is_dir($dir))
        {
            $files = [];
            //列出 dir 目录中的文件和目录：
            $list = scandir($dir);

            foreach($list as $key => $value)
            {
                if($value != '.' && $value != '..')
                {
                    $isdir = $dir . '/' .$value;
                    if(is_dir($isdir))
                    {
                        $files[] = $this->dir($isdir);
                    }
                    else
                    {
                        echo $value.'<br />';
                        $files[] = $value;
                    }
                }
            }
            return $files;
        }
    }




    public function test(Request $request){

        $arr = ['avahi-libs','libssh2','kernel','kernel-headers','kernel-tools','kernel-tools-libs','python-perf','httpd','httpd-tools','rsyslog','elfutils-default-yama-scope','elfutils-libelf','elfutils-libs','python','python-libs','libX11','libX11-common','libssh2','python','python-libs','bash','python','python-libs','httpd','httpd-tools','sqlite','nss-softokn','nss-softokn-freebl','nss-util','nss','nss-sysinit','nss-tools','libssh2','bind-libs-lite','bind-license','rsyslog','openssh','openssh-clients','openssh-server','expat','cups-client','cups-libs','unzip','wget','sudo','procps-ng','mariadb','mariadb-libs','git','perl-Git','bind-libs-lite','bind-license','kernel','kernel-headers','kernel-tools','kernel-tools-libs','python-perf','curl','libcurl','ntp','ntpdate','nspr','nss-util','nss-softokn','nss-softokn-freebl','nss','nss-sysinit','nss-tools','patch','kernel','kernel-headers','kernel-tools','kernel-tools-libs','python-perf','polkit','binutils','bind-libs-lite','libjpeg-turbo','dhclient','dhcp-common','dhcp-libs','bind-libs-lite','bind-license','libtiff','patch','ghostscript','atk','libtiff','systemd','systemd-libs','systemd-networkd','systemd-sysv','perl','perl-Pod-Escapes','perl-libs','perl-macros','krb5-libs','bind-export-libs','libxml2','libxml2-devel','libxml2-python','ImageMagick'];
        $ver = ['avahi-libs-0.6.31-20.el7.x86_64','libssh2-1.8.0-3.el7.x86_64','kernel-tools-3.10.0-1127.19.1.el7.x86_64','kernel-3.10.0-693.el7.x86_64','kernel-3.10.0-1127.19.1.el7.x86_64','kernel-3.10.0-693.2.2.el7.x86_64','kernel-headers-3.10.0-1127.19.1.el7.x86_64','kernel-3.10.0-957.5.1.el7.x86_64','kernel-3.10.0-693.11.1.el7.x86_64','kernel-tools-libs-3.10.0-1127.19.1.el7.x86_64','kernel-headers-3.10.0-1127.19.1.el7.x86_64','kernel-tools-3.10.0-1127.19.1.el7.x86_64','kernel-tools-libs-3.10.0-1127.19.1.el7.x86_64','kernel-tools-libs-3.10.0-1127.19.1.el7.x86_64','python-perf-3.10.0-1127.19.1.el7.x86_64','httpd-tools-2.4.6-93.el7.centos.x86_64','httpd-2.4.6-93.el7.centos.x86_64','httpd-tools-2.4.6-93.el7.centos.x86_64','rsyslog-8.24.0-52.el7_8.2.x86_64','elfutils-default-yama-scope-0.176-4.el7.noarch','elfutils-libelf-0.176-4.el7.x86_64','elfutils-libs-0.176-4.el7.x86_64','python-slip-0.4.0-4.el7.noarch','python-urlgrabber-3.10-9.el7.noarch','python-libs-2.7.5-88.el7.x86_64','newt-python-0.52.15-4.el7.x86_64','python-gobject-base-3.22.0-1.el7_4.1.x86_64','python-pycurl-7.19.0-19.el7.x86_64','python-ipaddress-1.0.16-2.el7.noarch','libxml2-python-2.9.1-6.el7.4.x86_64','python-perf-3.10.0-1127.19.1.el7.x86_64','python-2.7.5-88.el7.x86_64','rpm-python-4.11.3-35.el7.x86_64','python-setuptools-0.9.8-7.el7.noarch','python-kitchen-1.1.1-5.el7.noarch','python-linux-procfs-0.4.9-4.el7.noarch','python-firewall-0.5.3-5.el7.noarch','python-decorator-3.4.0-3.el7.noarch','python2-pip-8.1.2-5.el7.noarch','dbus-python-1.1.1-9.el7.x86_64','python-schedutils-0.4-6.el7.x86_64','python-chardet-2.2.1-1.el7_1.noarch','python-slip-dbus-0.4.0-4.el7.noarch','libselinux-python-2.5-15.el7.x86_64','python-backports-1.0-8.el7.x86_64','python-backports-ssl_match_hostname-3.5.0.1-1.el7.noarch','python-pyudev-0.15-9.el7.noarch','python-iniparse-0.4-9.el7.noarch','python-configobj-4.7.2-7.el7.noarch','python-libs-2.7.5-88.el7.x86_64','libX11-common-1.6.7-2.el7.noarch','libX11-1.6.7-2.el7.x86_64','libX11-common-1.6.7-2.el7.noarch','libssh2-1.8.0-3.el7.x86_64','python-slip-0.4.0-4.el7.noarch','python-urlgrabber-3.10-9.el7.noarch','python-libs-2.7.5-88.el7.x86_64','newt-python-0.52.15-4.el7.x86_64','python-gobject-base-3.22.0-1.el7_4.1.x86_64','python-pycurl-7.19.0-19.el7.x86_64','python-ipaddress-1.0.16-2.el7.noarch','libxml2-python-2.9.1-6.el7.4.x86_64','python-perf-3.10.0-1127.19.1.el7.x86_64','python-2.7.5-88.el7.x86_64','rpm-python-4.11.3-35.el7.x86_64','python-setuptools-0.9.8-7.el7.noarch','python-kitchen-1.1.1-5.el7.noarch','python-linux-procfs-0.4.9-4.el7.noarch','python-firewall-0.5.3-5.el7.noarch','python-decorator-3.4.0-3.el7.noarch','python2-pip-8.1.2-5.el7.noarch','dbus-python-1.1.1-9.el7.x86_64','python-schedutils-0.4-6.el7.x86_64','python-chardet-2.2.1-1.el7_1.noarch','python-slip-dbus-0.4.0-4.el7.noarch','libselinux-python-2.5-15.el7.x86_64','python-backports-1.0-8.el7.x86_64','python-backports-ssl_match_hostname-3.5.0.1-1.el7.noarch','python-pyudev-0.15-9.el7.noarch','python-iniparse-0.4-9.el7.noarch','python-configobj-4.7.2-7.el7.noarch','python-libs-2.7.5-88.el7.x86_64','bash-4.2.46-34.el7.x86_64','python-slip-0.4.0-4.el7.noarch','python-urlgrabber-3.10-9.el7.noarch','python-libs-2.7.5-88.el7.x86_64','newt-python-0.52.15-4.el7.x86_64','python-gobject-base-3.22.0-1.el7_4.1.x86_64','python-pycurl-7.19.0-19.el7.x86_64','python-ipaddress-1.0.16-2.el7.noarch','libxml2-python-2.9.1-6.el7.4.x86_64','python-perf-3.10.0-1127.19.1.el7.x86_64','python-2.7.5-88.el7.x86_64','rpm-python-4.11.3-35.el7.x86_64','python-setuptools-0.9.8-7.el7.noarch','python-kitchen-1.1.1-5.el7.noarch','python-linux-procfs-0.4.9-4.el7.noarch','python-firewall-0.5.3-5.el7.noarch','python-decorator-3.4.0-3.el7.noarch','python2-pip-8.1.2-5.el7.noarch','dbus-python-1.1.1-9.el7.x86_64','python-schedutils-0.4-6.el7.x86_64','python-chardet-2.2.1-1.el7_1.noarch','python-slip-dbus-0.4.0-4.el7.noarch','libselinux-python-2.5-15.el7.x86_64','python-backports-1.0-8.el7.x86_64','python-backports-ssl_match_hostname-3.5.0.1-1.el7.noarch','python-pyudev-0.15-9.el7.noarch','python-iniparse-0.4-9.el7.noarch','python-configobj-4.7.2-7.el7.noarch','python-libs-2.7.5-88.el7.x86_64','httpd-tools-2.4.6-93.el7.centos.x86_64','httpd-2.4.6-93.el7.centos.x86_64','httpd-tools-2.4.6-93.el7.centos.x86_64','sqlite-3.7.17-8.el7_7.1.x86_64','nss-softokn-freebl-3.44.0-8.el7_7.x86_64','nss-softokn-3.44.0-8.el7_7.x86_64','nss-softokn-freebl-3.44.0-8.el7_7.x86_64','nss-util-3.44.0-4.el7_7.x86_64','nss-pem-1.0.3-5.el7.x86_64','nss-softokn-freebl-3.44.0-8.el7_7.x86_64','nss-tools-3.44.0-7.el7_7.x86_64','jansson-2.10-1.el7.x86_64','openssh-clients-7.4p1-21.el7.x86_64','nss-softokn-3.44.0-8.el7_7.x86_64','openssh-server-7.4p1-21.el7.x86_64','openssl-1.0.2k-19.el7.x86_64','nss-3.44.0-7.el7_7.x86_64','openssl-libs-1.0.2k-19.el7.x86_64','openssl-devel-1.0.2k-19.el7.x86_64','nss-util-3.44.0-4.el7_7.x86_64','nss-sysinit-3.44.0-7.el7_7.x86_64','openssh-7.4p1-21.el7.x86_64','nss-sysinit-3.44.0-7.el7_7.x86_64','nss-tools-3.44.0-7.el7_7.x86_64','libssh2-1.8.0-3.el7.x86_64','bind-libs-lite-9.11.4-16.P2.el7_8.6.x86_64','bind-license-9.11.4-16.P2.el7_8.6.noarch','rsyslog-8.24.0-52.el7_8.2.x86_64','openssh-clients-7.4p1-21.el7.x86_64','openssh-server-7.4p1-21.el7.x86_64','openssh-7.4p1-21.el7.x86_64','openssh-clients-7.4p1-21.el7.x86_64','openssh-server-7.4p1-21.el7.x86_64','expat-2.1.0-11.el7.x86_64','cups-client-1.6.3-43.el7.x86_64','cups-libs-1.6.3-43.el7.x86_64','unzip-6.0-21.el7.x86_64','wget-1.14-18.el7_6.1.x86_64','sudo-1.8.23-9.el7.x86_64','procps-ng-3.3.10-27.el7.x86_64','mariadb-libs-5.5.65-1.el7.x86_64','mariadb-5.5.65-1.el7.x86_64','mariadb-libs-5.5.65-1.el7.x86_64','linux-firmware-20191203-76.gite8a0f4c.el7.noarch','lm_sensors-libs-3.4.0-6.20160601gitf9185e5.el7.x86_64','git-1.8.3.1-23.el7_8.x86_64','crontabs-1.11-6.20121102git.el7.noarch','net-tools-2.0-0.24.20131004git.el7.x86_64','perl-Git-1.8.3.1-23.el7_8.noarch','bind-libs-lite-9.11.4-16.P2.el7_8.6.x86_64','bind-license-9.11.4-16.P2.el7_8.6.noarch','kernel-tools-3.10.0-1127.19.1.el7.x86_64','kernel-3.10.0-693.el7.x86_64','kernel-3.10.0-1127.19.1.el7.x86_64','kernel-3.10.0-693.2.2.el7.x86_64','kernel-headers-3.10.0-1127.19.1.el7.x86_64','kernel-3.10.0-957.5.1.el7.x86_64','kernel-3.10.0-693.11.1.el7.x86_64','kernel-tools-libs-3.10.0-1127.19.1.el7.x86_64','kernel-headers-3.10.0-1127.19.1.el7.x86_64','kernel-tools-3.10.0-1127.19.1.el7.x86_64','kernel-tools-libs-3.10.0-1127.19.1.el7.x86_64','kernel-tools-libs-3.10.0-1127.19.1.el7.x86_64','python-perf-3.10.0-1127.19.1.el7.x86_64','python-pycurl-7.19.0-19.el7.x86_64','libcurl-7.29.0-57.el7_8.1.x86_64','curl-7.29.0-57.el7_8.1.x86_64','libcurl-7.29.0-57.el7_8.1.x86_64','ntp-4.2.6p5-29.el7.centos.2.x86_64','fontpackages-filesystem-1.44-8.el7.noarch','ntpdate-4.2.6p5-29.el7.centos.2.x86_64','ntpdate-4.2.6p5-29.el7.centos.2.x86_64','nspr-4.21.0-1.el7.x86_64','nss-util-3.44.0-4.el7_7.x86_64','nss-softokn-freebl-3.44.0-8.el7_7.x86_64','nss-softokn-3.44.0-8.el7_7.x86_64','nss-softokn-freebl-3.44.0-8.el7_7.x86_64','nss-pem-1.0.3-5.el7.x86_64','nss-softokn-freebl-3.44.0-8.el7_7.x86_64','nss-tools-3.44.0-7.el7_7.x86_64','jansson-2.10-1.el7.x86_64','openssh-clients-7.4p1-21.el7.x86_64','nss-softokn-3.44.0-8.el7_7.x86_64','openssh-server-7.4p1-21.el7.x86_64','openssl-1.0.2k-19.el7.x86_64','nss-3.44.0-7.el7_7.x86_64','openssl-libs-1.0.2k-19.el7.x86_64','openssl-devel-1.0.2k-19.el7.x86_64','nss-util-3.44.0-4.el7_7.x86_64','nss-sysinit-3.44.0-7.el7_7.x86_64','openssh-7.4p1-21.el7.x86_64','nss-sysinit-3.44.0-7.el7_7.x86_64','nss-tools-3.44.0-7.el7_7.x86_64','patch-2.7.1-12.el7_7.x86_64','kernel-tools-3.10.0-1127.19.1.el7.x86_64','kernel-3.10.0-693.el7.x86_64','kernel-3.10.0-1127.19.1.el7.x86_64','kernel-3.10.0-693.2.2.el7.x86_64','kernel-headers-3.10.0-1127.19.1.el7.x86_64','kernel-3.10.0-957.5.1.el7.x86_64','kernel-3.10.0-693.11.1.el7.x86_64','kernel-tools-libs-3.10.0-1127.19.1.el7.x86_64','kernel-headers-3.10.0-1127.19.1.el7.x86_64','kernel-tools-3.10.0-1127.19.1.el7.x86_64','kernel-tools-libs-3.10.0-1127.19.1.el7.x86_64','kernel-tools-libs-3.10.0-1127.19.1.el7.x86_64','python-perf-3.10.0-1127.19.1.el7.x86_64','polkit-0.112-26.el7.x86_64','polkit-pkla-compat-0.1-4.el7.x86_64','binutils-2.27-43.base.el7_8.1.x86_64','bind-libs-lite-9.11.4-16.P2.el7_8.6.x86_64','libjpeg-turbo-1.2.90-8.el7.x86_64','dhclient-4.2.5-79.el7.centos.x86_64','dhcp-common-4.2.5-79.el7.centos.x86_64','dhcp-libs-4.2.5-79.el7.centos.x86_64','bind-libs-lite-9.11.4-16.P2.el7_8.6.x86_64','bind-license-9.11.4-16.P2.el7_8.6.noarch','libtiff-4.0.3-32.el7.x86_64','patch-2.7.1-12.el7_7.x86_64','libtiff-4.0.3-32.el7.x86_64','systemd-sysv-219-73.el7_8.9.x86_64','systemd-libs-219-73.el7_8.9.x86_64','systemd-219-73.el7_8.9.x86_64','systemd-libs-219-73.el7_8.9.x86_64','systemd-sysv-219-73.el7_8.9.x86_64','perl-podlators-2.5.1-3.el7.noarch','perl-macros-5.16.3-295.el7.x86_64','perl-Encode-2.51-7.el7.x86_64','perl-Exporter-5.68-3.el7.noarch','perl-Socket-2.010-4.el7.x86_64','perl-Scalar-List-Utils-1.27-248.el7.x86_64','perl-Time-HiRes-1.9725-3.el7.x86_64','perl-Error-0.17020-2.el7.noarch','perl-libs-5.16.3-295.el7.x86_64','perl-Pod-Perldoc-3.20-4.el7.noarch','perl-Pod-Usage-1.63-3.el7.noarch','perl-constant-1.27-2.el7.noarch','perl-Carp-1.26-244.el7.noarch','perl-Storable-2.45-3.el7.x86_64','perl-Filter-1.49-3.el7.x86_64','perl-Data-Dumper-2.145-3.el7.x86_64','perl-Test-Harness-3.28-3.el7.noarch','perl-Getopt-Long-2.40-3.el7.noarch','perl-parent-0.225-244.el7.noarch','perl-5.16.3-295.el7.x86_64','perl-threads-1.87-4.el7.x86_64','perl-threads-shared-1.43-6.el7.x86_64','perl-File-Temp-0.23.01-3.el7.noarch','perl-Pod-Simple-3.28-4.el7.noarch','perl-Thread-Queue-3.02-2.el7.noarch','perl-TermReadKey-2.30-20.el7.x86_64','perl-Git-1.8.3.1-23.el7_8.noarch','perl-HTTP-Tiny-0.033-3.el7.noarch','perl-Pod-Escapes-1.04-295.el7.noarch','perl-Text-ParseWords-3.29-4.el7.noarch','perl-Time-Local-1.2300-2.el7.noarch','perl-PathTools-3.40-5.el7.x86_64','perl-File-Path-2.09-2.el7.noarch','perl-Pod-Escapes-1.04-295.el7.noarch','perl-libs-5.16.3-295.el7.x86_64','perl-macros-5.16.3-295.el7.x86_64','krb5-libs-1.15.1-46.el7.x86_64','bind-export-libs-9.11.4-16.P2.el7_8.6.x86_64','libxml2-2.9.1-6.el7.4.x86_64','libxml2-python-2.9.1-6.el7.4.x86_64','libxml2-devel-2.9.1-6.el7.4.x86_64','libxml2-devel-2.9.1-6.el7.4.x86_64','libxml2-python-2.9.1-6.el7.4.x86_64'];
        $pim = new Pim();
        foreach($arr as $one){
            echo $one.',';
            foreach($ver as $v){
                $left = $pim->mbDiffStr($v,$one);
//                $left = ltrim($v,$one);

//                if($left != $v){
//                    echo '<pre>';
//                    var_dump(substr($left,0,1) == '-');
//                    var_dump(is_numeric(substr($left,1,1)));
//                    echo '</pre>';
//                    exit;
//                }
                if( ($left != $v) && (substr($left,0,1) == '-') && is_numeric(substr($left,1,1)) ){
                    echo (substr($left,1));
                    break;
                }
            }
            echo "\r\n";
        }
        echo '########test########';
        exit;


        $pService = new ProductService();
        $product = $pService->cacheProductInfoById(428);
        echo '<pre>';
        print_r($product);
        echo '</pre>';
        exit;
        echo '########test########';
        exit;


        $type = $request->type;
        if(!$type) return;
        $spus = $ret = [];
        $base_dir = 'D:/资料/Sisley/materials';
        switch ($type){
            case 'spu_pdp':
                $query = DB::table('tb_product_detail as pd');
                $data = $query->leftJoin('tb_product as p', 'pd.product_idx', '=', 'p.id')->where('detail','!=','')->where('p.status',1)->get()->toArray();
                $data = object2Array($data);
                foreach($data as $one){
                    $tags = json_decode($one['detail'],true);
                    if(!$tags) {
//                        echo $one['product_id'].'无主图'.PHP_EOL;
                        continue;
                    }

                    $n = 1;
                    foreach($tags as $k=>$tag){
                        $a = 1;
                        if(empty($tag['tag']) || (!in_array($tag['tag'],['image','multi_image','product_recommend'])))  continue;
                        $path = '';

                        if( ($tag['tag'] == 'image') && !empty($tag['src']['pc'])){
                            $path = $tag['src']['pc'];
                            $arr = explode('.',$path);
                            $ext = $arr[count($arr)-1];
                            $ret[] = [
                                'name'=>$base_dir.'/pdp/prddetails/'.$one['product_id'].'_'.($n).'.'.$ext,
                                'path'=>$path,
                            ];
                        }

                        if( ($tag['tag'] == 'multi_image') && !empty($tag['nodes'])){
                            foreach($tag['nodes'] as $node){
                                if(empty($node['src'])) continue;
                                $path = $node['src'];
                                $arr = explode('.',$path);
                                $ext = $arr[count($arr)-1];
                                $ret[] = [
                                    'name'=>$base_dir.'/pdp/prddetails/'.$one['product_id'].'_'.($n).'#'.$a.'.'.$ext,
                                    'path'=>$path,
                                ];
                                $a++;
                            }
                        }

                        if( ($tag['tag'] == 'product_recommend') && !empty($tag['nodes'])){
                            foreach($tag['nodes'] as $node){
                                if(empty($node['src'])) continue;
                                $path = $node['src'];
                                $arr = explode('.',$path);
                                $ext = $arr[count($arr)-1];
                                $ret[] = [
                                    'name'=>$base_dir.'/pdp/prddetails/'.$one['product_id'].'_'.($n).'~'.$a.'.'.$ext,
                                    'path'=>$path,
                                ];
                                $a++;
                            }

                        }
                        $n++;
                    }

                }


                echo json_encode($ret);

                break;
            case 'sku':
                $query = DB::table('tb_prod_sku as s');
                $data = $query->where('kv_images','!=','')->get()->toArray();
                $data = object2Array($data);
                foreach($data as $one){
                    $imgs = json_decode($one['kv_images'],true);
                    if(!$imgs) {
//                        echo $one['product_id'].'无主图'.PHP_EOL;
                        continue;
                    }

                    foreach($imgs as $k=>$img){
                        if(empty($img['tag']) || (!in_array($img['tag'],['image','video'])))  continue;
                        $path = '';


                        if( ($img['tag'] == 'image') &&  !empty($img['data']['src'])) $path = $img['data']['src'];
                        if( ($img['tag'] == 'video') &&  !empty($img['data']['video'])) $path = $img['data']['video'];

                        $arr = explode('.',$path);
                        $ext = $arr[count($arr)-1];

//                        $ret[] = $one['product_id'].'_'.($k+1).'.'.$ext.','.$path;

                        $ret[] = [
                            'name'=>$base_dir.'/pdp/headimgs/'.$one['sku_id'].'_'.($k+1).'.'.$ext,
                            'path'=>$path,
                        ];
                    }

                }


                echo json_encode($ret);

                break;
                break;
            case 'spu_kv':
                $query = DB::table('tb_product as p');
                $data = $query->where('kv_images','!=','')->where('p.status',1)->get()->toArray();
                $data = object2Array($data);
                foreach($data as $one){
                    $imgs = json_decode($one['kv_images'],true);
                    if(!$imgs) {
//                        echo $one['product_id'].'无主图'.PHP_EOL;
                        continue;
                    }

                    foreach($imgs as $k=>$img){
                        if(empty($img['tag']) || (!in_array($img['tag'],['image','video'])))  continue;
                        $path = '';


                        if( ($img['tag'] == 'image') &&  !empty($img['data']['src'])) $path = $img['data']['src'];
                        if( ($img['tag'] == 'video') &&  !empty($img['data']['video'])) $path = $img['data']['video'];

                        $arr = explode('.',$path);
                        $ext = $arr[count($arr)-1];

//                        $ret[] = $one['product_id'].'_'.($k+1).'.'.$ext.','.$path;

                        $ret[] =    [
                            'name'=>$base_dir.'/pdp/plpspuimg/'.$one['product_id'].'_'.($k+1).'.'.$ext,
                            'path'=>$path,
                        ];
                    }

                }


                echo json_encode($ret);

                break;
            case 'plp':
                $query = DB::table('tb_category as c');
                $data = $query->where('cat_kv_image','!=','')->where('status',1)->get()->toArray();
                $data = object2Array($data);
                foreach($data as $one){
                    $cats = [];
                    Category::getParentsByCatId($one['id'],$cats);
                    $arr = explode('.',$one['cat_kv_image']);
                    $ext = $arr[count($arr)-1];
                    if(count($cats) == 2) $name = $base_dir.'/plp/secondlevel/'.$one['id'].$one['cat_name'].'/kv.'.$ext;
                    if(count($cats) >= 3) $name = $base_dir.'/plp/thirdlevel/'.$one['id'].$one['cat_name'].'/kv.'.$ext;
                    if(empty($name)) continue;

                    $ret[] =    [
                        'name'=>$name,
                        'path'=>$one['cat_kv_image'],
                    ];

                }

                echo json_encode($ret);
                break;
        }
        exit;





//        $ret = [];
//        Category::getChildrenByCatIds([13],$ret);
//        dd($ret);
//        dd(Spu::getProductInfoById(10));

//        $redis = ProductService::getRedis();
//        if(!$redis->set('sssss',1,'ex',3,'nx')) return $this->error('请求过于频繁');
//        echo '########test########';
//        exit;

//        $adService = new AdService();
////        $ads = $adService->cacheAllLocAds('product_list_ad');
//        $ads = $adService->getLocAdsFromCache('product_list_ad');
//        dd($ads);

//        dd(Config::getConfigByName('rec'));

        $pService = new ProductService();
//        $product = $pService->cacheProductInfoById(10);
//        $product = $pService->getProductInfoFromCache(10);
//        $product = $pService->cacheCollectionInfoById(30);
        $product = $pService->getColletionInfoFromCache(30);

        dd($product);

        Category::getParentsByCatId(17,$cats);
        dd(array_reverse($cats));
        $cService = new CategoryService();
        $cService->cacheCategoryInfoById();
    }


    /**
     * 产品列表，支持模糊查询.
     */
    public function list(Request $request)
    {
        $limit = $request->limit ?: 10;
        $retSku = $request->retSku?1:0;
        $query = new Spu();
        $query = $query->orderBy('id','desc');
        $pService = new ProductService();

        if (isset($request->product_type) && $request->product_type !== '') {
            $query = $query->selectRaw('tb_product.*,min(s.ori_price)')->leftJoin('tb_prod_sku as s', 's.product_idx', '=', 'tb_product.id')->where('tb_product.id','>',0)->groupBy('tb_product.id')->havingRaw('min(s.ori_price)'.(empty($request->product_type)?'=':'>').'0');
        }

        if ($request->product_name) {
            $query = $query->where('product_name', 'like', '%' . $request->product_name . '%');
        }

        if ($request->product_id) {
            $query = $query->where('product_id', '=', $request->product_id);
        }

        if ($request->cat_id) {
            $products = ProductCat::getCatProductsById($request->cat_id);
            $pids = array_column($products,'id');
            $query = $query->whereIn('tb_product.id', $pids);
        }

        if (isset($request->status) && $request->status !== '') {
            $query = $query->where('status',  $request->status);
        }

        $deProdData = $query->paginate($limit)->toArray();

        if($retSku){
            $prodIdxs = array_column($deProdData['data'],'id');
            $products = Spu::batchGetProductsInfoByPid($prodIdxs,true);
        }

        foreach ($deProdData['data'] as &$prodData) {
            $prodData['product_type'] = 1;  //1商品 2商品集合
            if($retSku){
                if(!empty($products[$prodData['id']])){
                    $prodData = array_merge($prodData,$products[$prodData['id']]);
                }
            }
        }

        $return = [];
        $return['pageData'] = $pService->batchFormatProduct($deProdData['data']) ;
        if($request->retSpecs) {
            $specs = Spec::batchGetSpecBySpecTypes(['capacity_ml','capacity_g','color']);
            $return['specs'] = $specs;
        }
        $return['count'] = $deProdData['total'];

        return $this->success($return);
    }


    public function getProdOrCollList(Request $request){
        $type = in_array($request->type,['collection','product'])?$request->type:'product';
        if($type == 'collection'){
            $request->colle_name = $request->product_name;
            $request->colle_id = $request->product_id;
//            $request->page = $request->page - ceil( ($res['data']['count']??0)/10 );
//            $request->page = 1;
            $colle_controller = new CollectionController();
            $res = $colle_controller->list($request);
        }else{
            $res = $this->list($request);
        }
        return $res;
    }



    public function handleSpuCsv($data){
        $num = 0;
        foreach($data as $fields){
            if(count($fields) != 6) continue;
            $num++;
            $fields = array_map('trim',$fields);
            Spu::updateOrCreate(
                ['product_id' => $fields[0]],
                [
                    'product_name'=>$fields[1],
                    'product_name_en'=>$fields[2],
                    'spec_type'=>$fields[3],
                    'product_desc'=>$fields[4],
                    'short_product_desc'=>$fields[5],
                ]
            );
        }
        return $num;
    }
    public function handleSkuCsv($data){
        $num = 0;
        foreach($data as $fields){
            if(count($fields) != 5) continue;
            $product_id = $fields[2];
            if(!$product_id) continue;
            if(!$product = Spu::getProductInfoByProductId($product_id)) continue;
            $spec_type = explode(',',$product['spec_type'])[0]??'';
            if(!$spec_type) continue;
            $spec_field = Sku::SPEC_FIELD_MAP[$spec_type]??'';
            if(!$spec_field) continue;
            if(!in_array($fields[4],array_keys(Sku::REVENUE_TYPE_MAP))) continue;   //税收发票类型不对

            $num++;
            $fields = array_map('trim',$fields);
            Sku::updateOrCreate(
                ['sku_id' => $fields[0]],
                [
                    'sku_id' => $fields[0],
                    $spec_field=>$fields[1],
                    'product_idx'=>$product['id'],
                    'ori_price'=>$fields[3],
                    'revenue_type'=>$fields[4],
                    'size'=>$fields[5],
                ]
            );
        }
        return $num;

    }
    public function handleSpecCsv($data){
        $num = 0;
        foreach($data as $fields){
            if(count($fields) != 5) continue;
            $num++;
            $fields = array_map('trim',$fields);
            Spu::updateOrCreate(
                ['spec_code' => $fields[0],'spec_type'=>$fields[1]],
                [
                    'spec_code' => $fields[0],
                    'spec_type'=>$fields[1],
                    'spec_unit'=>$fields[2],
                    'spec_property'=>$fields[3],
                    'spec_desc'=>$fields[4],
                ]
            );
        }
        return $num;
    }

    //商品和商品集合列表
    public function getCatProdAndColleList(Request $request){
        $cat_id = $request->cat_id;
        if(!$cat_id) return $this->error("参数缺失");
        $res = ProductCat::getProdAndColleById($request->cat_id);
        $ret = $res['all']??[];
        return $this->success($ret);

    }

    public function updateCatRelation(Request $request){
        $id = $request->id;
        $sort = $request->sort;
        if(!$id || is_null($sort)) return $this->error("参数缺失");

        $upData = [
            'update_time'=>time(),
            'sort'=>$sort,
        ];
        $upNum = ProductCat::updateById($id,$upData);
        if($upNum){
            return $this->success("更新成功");
        }
        return $this->success("更新失败了");
    }

    public function add(Request $request){
        $all = $request->all();

        $fields = [
            'product_id' => 'required',
            'product_name' => 'required',
//            'spec_type'  => 'required'
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required'   => '请输入:attribute', // :attribute 字段占位符表示字段名称
//            'string'     => ':attribute 为字符串',
//            'min'       => ':attribute至少:min位字符长度'
            ]
        );

        $specType = $request->spec_type??[];
        $allSpecType = array_keys(Sku::SPEC_FIELD_MAP);
        $legalSpecType = array_diff((array)$specType,(array)$allSpecType);
        if($specType && count($legalSpecType) != 0)
            return $this->error("规格类型不合法");

        if($validator->fails()){
            return $this->error($validator->errors()->first());
        }
        $spuModel = new Spu();
        $spuModel->product_id = $all['product_id'];
        $spuModel->product_name = $all['product_name'];
        $spuModel->product_name_en = $all['product_name_en']??'';
        $spuModel->list_name = $all['list_name']??'';
        $spuModel->short_product_desc = $all['short_product_desc']??'';
        $spuModel->custom_keyword = $all['custom_keyword']??'';
        $spuModel->product_desc = $all['product_desc']??'';
        $spuModel->spec_type = $specType?implode(',',$specType):'';
        $spuModel->priority_cat_id = $all['priority_cat_id']??0;
        $spuModel->share_img = $all['share_img']??'';
        $spuModel->list_img = $all['list_img']??'';
        $spuModel->is_gift_box = $all['is_gift_box']??'';
        $spuModel->sort = $all['sort']??'';
        $spuModel->score = $all['score']??'';
        $spuModel->can_search = $all['can_search']??1;
        $spuModel->display_start_time = !empty($all['display_start_time'])?strtotime($all['display_start_time']):0;
        $spuModel->display_end_time = !empty($all['display_end_time'])?strtotime($all['display_end_time']):0;
        $spuModel->tag = $all['tag']??'';
        $spuModel->rec_cat_id = $all['rec_cat_id']??'';
        $spuModel->status = 0;
        $spuModel->type = $all['type']??1;
        $spuModel->insert_type = $all['insert_type']??1;
        if($spuModel->save()) return $this->success("创建商品成功");
        return $this->error("新增失败了");
    }

    //检查规格是否合法
    public function checkSpec(Request $request){
        $specType = $request->specType;
        $spec = $request->spec;
        if(empty($specType) || empty($spec) ) return $this->success(['legal'=>0]);
        $specs = Spec::batchGetSpecBySpecTypes([$specType]);
        $specs = !empty($specs[$specType])?array_column($specs[$specType],'spec_code'):[];
        if(empty($specs)) return $this->success(['legal'=>0]);
        return $this->success(['legal'=>in_array($spec,$specs)?1:0]);
    }

    /**
     * 后端产品列表，支持模糊查询.
     */
    public function backList(Request $request)
    {
        $limit = $request->limit ?: 10;
        $query = new Spu();
        $pService = new ProductService();
        if ($request->prodName) {
            $query = $query->where('product_name', 'like', '%' . $request->product_name . '%');
        }
        if ($request->status) {
            $query = $query->where('status',  $request->status);
        }

        if ($request->cat_id) {
            $products = ProductCat::getCatProductsById($request->cat_id);
            $pids = array_column($products,'id');
            $query = $query->whereIn('id', $pids);
        }

        $deProdData = $query->paginate($limit)->toArray();

        $return = [];
        $return['pageData'] =  $pService->batchFormatProduct($deProdData['data']) ;
        $return['count'] = $deProdData['total'];

        return $this->success($return);
    }

    /**
     * 获取产品.
     */
    public function getProd(Request $request)
    {
        $prodIdx = $request->id;
        $prodInfo = Spu::getProductInfoById($prodIdx);
        $pService = new ProductService();
        $prodInfo = $pService->formatProduct($prodInfo);
        $specs = Spec::SPEC_NAME_MAP;
        $productCats = ProductCat::getProductCatsByPidx($prodIdx);
        $return = [];
        $return['data'] = $prodInfo;
        $return['specs'] = $specs;
        $return['cats'] = $productCats??[];

        return json_encode($return);
    }

    /**
     * 编辑产品.
     */
    public function editProd(Request $request)
    {

        $all = $request->all();
        $prodIdx = $request->id;

        $fields = [
//            'product_id' => 'required',
//            'product_name' => 'required',
//            'spec_type'  => 'required'
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required'   => '请输入:attribute', // :attribute 字段占位符表示字段名称
//            'string'     => ':attribute 为字符串',
//            'min'       => ':attribute至少:min位字符长度'
            ]
        );

        $specType = $request->spec_type;
        if($specType){
            $allSpecType = array_keys(Sku::SPEC_FIELD_MAP);
            $legalSpecType = array_diff((array)$specType,(array)$allSpecType);
            if(count($legalSpecType) != 0)
                return $this->error(0,"规格类型不合法");
        }


        if($validator->fails()){
            return $this->error(0,$validator->errors()->first());
        }

        $data = [
            'product_name'=>$all['product_name'],
            'product_name_en'=>$all['product_name_en']??'',
            'list_name'=>$all['list_name']??'',
            'custom_keyword'=>$all['custom_keyword']??'',
            'spec_type'=>$specType?implode(',',$specType):'',
            'product_desc'=>$all['product_desc']??'',
            'short_product_desc'=>$all['short_product_desc']??'',
            'priority_cat_id'=>$all['priority_cat_id']??0,
            'display_start_time'=>!empty($all['display_start_time'])?strtotime($all['display_start_time']):0,
            'display_end_time'=>!empty($all['display_end_time'])?strtotime($all['display_end_time']):0,
            'can_search'=>$all['can_search']??1,
            'tag'=>$all['tag']??'',
            'rec_cat_id'=>$all['rec_cat_id']??'',
            'rec_spu'=>$all['rec_spu']??'',
            'share_img'=>$all['share_img']??'',
            'list_img'=>$all['list_img']??'',
            'is_gift_box'=>$all['is_gift_box']??'',
            'sort'=>$all['sort']??'',
            'score'=>$all['score']??'',
//            'detail_images'=>$all['detail_images']??'',
        ];
        try{
            $upNum = Spu::where('id', $prodIdx)->first()->update($data);
            // $upNum = Spu::updateById($prodIdx,$data);
            $pService = new ProductService();
            $pService->cacheProductInfoById($prodIdx);
            if($upNum) return $this->success("更新成功");
            return $this->error("更新失败");
        }catch (\Exception $e){
            return $this->error("更新失败了");
        }
    }


    /**
     * 修改产品上下架状态.
     */
    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $allData = $request->all();
        $upData = [];
        foreach($allData as $k=>$v){
            if(($k == 'status') && ($v == 1) ){
                $spu = Spu::getProductInfoById($id,true);
                if(empty($spu['skus'])) return $this->error(0,'skus 为空，无法上架');
            }
            if(in_array($k,Spu::$fields)){
                $upData[$k] = $v;
            }
        }


        $upNum = Spu::updateById($id,$upData);
        if($upNum){
            $pService = new ProductService();
            $pService->cacheProductInfoById($id);
        }
        if(empty($upNum)){
            return $this->error(0,'更新失败');
        }else{
            return $this->success([]);
        }
    }

    /**
     * 获取关联SKU关系.
     */
    public function relateSkus(Request $request)
    {
        $prodIdx = $request->prodIdx;
        $prodSkusList = DB::table('css_prod_sku_relation')->leftJoin('css_ec_skus_info', 'css_prod_sku_relation.sku_idx', '=', 'css_ec_skus_info.id')->where('css_prod_sku_relation.product_idx', $prodIdx)->orderBy('css_prod_sku_relation.sort', 'ASC')->get()->toArray();
        $return = [];
        $return['data'] = $prodSkusList;

        return json_encode($return);
    }


    /**
     * 编辑关联SKU关系.
     */
    public function editRelateSkus(Request $request)
    {
        $rawData = json_decode($request->data, true);
        try {
            DB::beginTransaction();
            foreach ($rawData as $key => $data) {
                $_tmpUData = [];
                $_tmpUData['product_idx'] = $data['product_idx'];
                $_tmpUData['sku_idx'] = $data['sku_idx'];
                $_tmpUData['hash'] = md5($_tmpUData['product_idx'] . '###' . $_tmpUData['sku_idx']);
                $_tmpUData['sort'] = $key + 1;
                SpuToSku::updateOrCreate(
                    ['hash' => $_tmpUData['hash']],
                    $_tmpUData
                );
            }
            DB::commit();

            return $this->success([]);
        } catch (Exception $e) {
            DB::rollBack();

            return $this->error(0, $e->getMessage());
        }
    }

    /**
     * 修改产品上下架状态.
     */
    public function changeDisplay(Request $request)
    {
        $prodIdx = $request->prodIdx;
        $status = $request->status;

        $spuInfo = Spu::where('id', $prodIdx)->first()->toArray();

        if (empty($spuInfo)) {
            return $this->error(0, '未找到产品');
        }

        $path = '';
        if (empty($spuInfo['qr_code'])) {
            $WechatHelp = new WechatHelp();
            $qrReturn = $WechatHelp->generateQrCode($spuInfo['product_id'], 'pages/pdt/pdt-detail/pdt-detail', '1280', true);
            if (false !== $qrReturn) {
                $ossClient = new \App\Lib\Oss();
                $remoteFilePath = 'miniStore/qr-code/' . $qrReturn['fileName'];
                $ossBack = $ossClient->upload($remoteFilePath, $qrReturn['path']);
                if (true === $ossBack) {
                    $path = 'https://wecassets.chowsangsang.com.cn/' . $remoteFilePath;
                }
            }
        }

        $displayStatus = '1' === $status ? 1 : 0;
        $prodId = $spuInfo['product_id'];

        $update = [];
        $update['display_status'] = $displayStatus;
        if ('' !== $path) {
            $update['qr_code'] = $path;
        }
        Spu::where('id', $prodIdx)->update($update);

        $cacheDisplayStatus = $displayStatus === 0 || !empty($spuInfo['deleted_at']) ? 0 : 1;
        $RedisModel = new RedisModel();
        $RedisModel->_hset(config('redis.prodDisplay'), $prodId, $cacheDisplayStatus);
        //上下架更新上架时间表
        if (1 === $cacheDisplayStatus) {
            $updateTime = time();
            $RedisModel->_zadd(config('redis.prodDisplayDate'), $updateTime, $prodId);
            $RedisModel->_hset(config('redis.hProdDisplayDate'), $prodId, $updateTime);
        } else {
            $RedisModel->_zrem(config('redis.prodDisplayDate'), $prodId);
            $RedisModel->_hdel(config('redis.hProdDisplayDate'), $prodId);
        }

        $prodSkusRelation = $RedisModel->_zrevrange(config('redis.mappingProdSku') . '###' . $prodId, 0, -1);
        $insertSkusStatus = [];
        foreach ($prodSkusRelation as $sku) {
            $insertSkusStatus[$sku] = $cacheDisplayStatus;
        }
        $RedisModel->_hmset(config('redis.skuDisplay'), $insertSkusStatus);

        return 1;
    }

    public function createCharme(Request $request)
    {
        $sheetData = $request->sheetData;

        $spuArr = $sheetData['spu'];
        $skuArr = $sheetData['sku'];
        $spu = $spuArr[0];
        $exception = DB::transaction(function () use ($spu, $skuArr) {
            Spu::updateOrCreate(
                ['product_id' => $spu['product_id']],
                $spu
            );
            foreach ($skuArr as $sku) {
                if (!empty($sku['sku'])) {
                    Sku::updateOrCreate(
                        ['sku' => $sku['sku']],
                        $sku
                    );
                }
            }
        });

        return is_null($exception) ? 1 : 0;
    }

    public function saveDetail(Request $request){
        $all = $request->all();

        $fields = [
            'kv_images' => 'required',
            'id' => 'required',
            //'wechat'  => 'required',
            //'pc'  => 'required'
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required'   => '请输入:attribute', // :attribute 字段占位符表示字段名称
//            'string'     => ':attribute 为字符串',
//            'min'       => ':attribute至少:min位字符长度'
            ]
        );

        if($validator->fails()){
            return $this->error($validator->errors()->first());
        }

        $id = $all['id'];
        $kv_images = json_decode($all['kv_images'],true)??[];
//        if(!empty($all['kv_video'])) $kv_video = json_decode($all['kv_video'],true);


        $db_kv_images = $kv_images??[];
//        if(!empty($kv_video)) array_push($db_kv_images,$kv_video);
        Spu::where('id',$id)->update(['kv_images'=>json_encode($db_kv_images)]);
        if(isset($all['wechat']) && $all['wechat']){

            SpuDetail::updateOrCreate(
                ['channel'=>'wechat','product_idx'=>$id],
                ['detail'=>$all['wechat']]
            );
        }
        if(isset($all['pc']) && $all['pc']){

            SpuDetail::updateOrCreate(
                ['channel'=>'pc','product_idx'=>$id],
                ['detail'=>$all['pc']]
            );
        }
        return $this->success("",'更新成功');

    }

    public function getDetail(Request $request){

        $fields = [
            'id' => 'required',
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required'   => '请输入:attribute', // :attribute 字段占位符表示字段名称
//            'string'     => ':attribute 为字符串',
//            'min'       => ':attribute至少:min位字符长度'
            ]
        );

        if($validator->fails()){
            return $this->error($validator->errors()->first());
        }

        $detail = SpuDetail::getDetailsByPid($request->id);

        return $this->success($detail,'更新成功');

    }

    public function handleCsv(Request $request){
//        $method = $request->handleMethod;
        $data = $request->data;
        $data = json_decode($data,true);
        if(empty($data)) return $this->error("参数缺失");
        if(empty($data)) return $this->error(0,"参数缺失");
        $spu_num = $sku_num = $spec_num = 0;

        foreach($data as $one){
            $pid = 0;
            if(count($one) < 12) continue;
            $one = array_map('trim',$one);
            $spu = $sku = $spec = [];
            if($one[0]){
                $sku['sku_id'] = $one[0];
            }
//            $spec_type = $one[7];
            $spec_field = '';
            if($spec_type = $one[7]){
                $spec_field = Sku::SPEC_FIELD_MAP[$spec_type]??'';
                if(!$spec_field) continue;
                $spec['spec_type'] = $spec_type;
                if($one[1]) {
                    $sku[$spec_field] = $one[1];
                    $spec['spec_code'] = $one[1];
                }
            }
            $db_spu = [];
            if($one[2]) {
                $spu['product_id'] = $one[2];
                $db_spu = Spu::where('product_id',$spu['product_id'])->first();
            }

            if($db_spu){
                $sku['product_idx'] = $db_spu['id'];
            }
            if($one[3] !== '') $sku['ori_price'] = $one[3];
            if($one[4]) $sku['revenue_type'] = $one[4];
            if($one[5]) $spu['product_name'] = $one[5];
            if($one[6]) $spu['product_name_en'] = $one[6];
            if($one[7]) $spu['spec_type'] = $spec_type;
            if($one[8]) $spu['product_desc'] = $one[8];
            if($one[9]) $spu['short_product_desc'] = $one[9];
            if($one[10] || $one[1]) $spec['spec_desc'] = $one[10]?:$one[1];
            if($one[10] || $one[1]){
                if($spec_field && $one[10]) $sku[$spec_field.'_desc'] = $one[10];

                $spec['spec_desc'] = $one[10]?:$one[1];
            }
            if($one[11]) $sku['size'] = $one[11];

            if($spu && $db_spu){
                $spu['status'] = 1;
                Spu::where(['product_id'=>$spu['product_id']])->update($spu);
                $spu_num++;
            }

            if($spu && !empty($spu['product_id'])){
                if(!$db_spu){
                    $spu['status'] = 1;
                    $pid = Spu::insertGetId($spu);
                }
//                Spu::updateOrCreate(
//                    ['product_id' => $spu['product_id']],
//                    $spu
//                );
                $spu_num++;
            }

            if($sku && !empty($sku['sku_id'])){
                $db_sku = Sku::where('sku_id',$sku['sku_id'])->first();
                if($db_sku){
                    if(!empty($pid)) $sku['product_idx'] = $pid;
                    Sku::where('sku_id',$sku['sku_id'])->update($sku);
                }else{
                    if(empty($sku['product_idx']) && $pid) $sku['product_idx'] = $pid;
                    if(!empty($sku['product_idx'])) Sku::insertGetId($sku);
                }
                $sku_num++;

//                Sku::updateOrCreate(
//                    ['sku_id' => $sku['sku_id']],
//                    $sku
//                );
//                $sku_num++;
            }



            if($spec && !empty($spec['spec_code']) && !empty($spec['spec_type']) ){
                Spec::updateOrCreate(
                    ['spec_code' => $spec['spec_code'],'spec_type'=>$spec['spec_type']],
                    $spec
                );
                $spec_num++;
            }
        }

//        $num = $this->$method($data);
        return $this->success(['spu_num'=>$spu_num,'sku_num'=>$sku_num,'spec_num'=>$spec_num]);
    }

    public function syncCatPid(){
        $ids = [];
        foreach ($ids as $id){
            $cats = [];
            Category::getParentsByCatId($id,$cats);
            $prods = ProductCat::where('cat_id',$id)->get()->toArray();
            foreach($cats as $cat){
                if($cat['id'] == $id) continue;

                foreach($prods as $prod){
                    $data = [
                        'product_idx'=>$prod['product_idx'],
                        'cat_id'=>$cat['id'],
                        'created_at'=>'2020-08-14 10:00:00',
                        'type'=>$prod['type'],
                        'sort'=>$prod['sort'],
                    ];
                    $ins_id = ProductCat::firstOrCreate(['product_idx'=>$prod['product_idx'],'cat_id'=>$cat['id'],'type'=>$prod['type']],$data);
                    echo $ins_id.PHP_EOL;
                }

//                ProductCat::getProdAndColleById($cat['id']);
            }
        }
    }

    /**
     * 导入spu（有数）历史数据
     */
    public function exportSpuHistory(Request $request)
    {
        $data = Spu::exportSpuHistory();
        return $this->success($data, 'success');
    }
}

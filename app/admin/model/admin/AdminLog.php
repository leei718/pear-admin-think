<?php
declare (strict_types = 1);

namespace app\admin\model\admin;
use think\facade\Request;
use think\facade\Session;
use think\Model;

/**
 * @mixin \think\Model
 */
class AdminLog extends Model
{

    protected $table = 'admin_admin_log';
    
    public function log()
    {
        return $this->belongsTo('Admin','uid','id');
    }

    // 管理员日志记录
    public static function record()
    {
        if(!$desc = Request::except(['s','_pjax']))return;
        if(isset($desc['page'])&&isset($desc['limit']))return;
        foreach ($desc as $k => $v) {
            if (is_string($v) && strlen($v) > 255 || stripos($k, 'password') !== false)  {
                unset($desc[$k]);
            }
        }
        $info = [
           'uid'       => Session::get('admin.id'),
           'url'      => Request::url(),
           'desc'    => json_encode($desc), 
           'ip'       => Request::ip(),
           'user_agent'=> Request::server('HTTP_USER_AGENT'),

        ];
        $res = self::where('uid',$info['uid'])
            ->order('id', 'desc')
            ->find();
        if (isset($res['url'])!==$info['url']) {
            self::create($info);
        }
    }
}

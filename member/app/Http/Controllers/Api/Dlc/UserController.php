<?php namespace App\Http\Controllers\Api\Dlc;

use App\Exceptions\ApiPlaintextException;
use App\Model\Users;
use App\Service\Dlc\{UsersService};
use Illuminate\Http\Request;
use Validator;

class UserController extends ApiController
{
    public function userCenter(Request $request)
    {
        try {
            $uid = $this->getUid(1);
            $data = [];
            if ($uid) {
                $user = UsersService::getUserById($uid);
                $data = UsersService::getUserCenter($user);
                if ($guide = $user->guide) {
                    $guide_id = $guide->id;
                    $guide_name = $guide->name;
                }
            }
            //获取用户类型
            $userType = UsersService::getUserType($user ?? null);
            $data['userType'] = $userType;
            $data['balance'] = $user['balance'] ?? '0';
            //是否授权用户信息
            $userAuthor = UsersService::getUserAuthor($user ?? null);
            $data['userAuthor'] = $userAuthor;
            $data['guide'] = [
                'id' => $guide_id ?? '',
                'name' => $guide_name ?? '',
            ];
            //获取广告栏位
            $data['adInfo'] = UsersService::getCenterAd($userType);
            $data['inactive'] = config('dlc.inactive');
            $data['inactive2'] = config('dlc.inactive2');
            $data['redirect'] = [
                'appId' => 'wx986d1817076b7bdd',
                'path' => 'pages/index/index',
                'extraData' => [
                    'foo' => 'bar',
                ],
                'envVersion' => 'release',
            ];
            return $this->success('成功', $data);
        } catch (ApiPlaintextException $e) {
            return $this->expire();
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

    }

    public function userInfoShow(Request $request)
    {
        try {
            $uid = $this->getUid();
            $userInfo = UsersService::getUserProfile($uid);
            return $this->success('OK', $userInfo);
        } catch (ApiPlaintextException $e) {
            return $this->expire();
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function userInfoUpdate(Request $request)
    {
        try {
            $params = $request->all();
            $v = Validator::make($params, [
                'birthday' => 'nullable|date',
                'email' => 'nullable|email',
                'gender' => 'nullable',
                'name' => [
                    'nullable',
                    'string',
                    'max:20',
                    'regex:/^([a-zA-Z]|[0-9]|[\x{4e00}-\x{9fa5}]|[\s\-\_])*$/u'
                ],
            ], [
//                'birthday.required' => '生日不能为空',
                'birthday.date' => '生日格式不正确',
                'name.required' => '姓名不能为空，必须为汉字或字母',
                'name.max' => '姓名长度不能超过20位字符',
                'name.regex' => '姓名不支持特殊字符',
                'email' => '邮箱格式错误',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $uid = $this->getUid();
            $result = UsersService::updateUserProfile(
                $uid,
                $request->get('name'),
                $request->get('gender'),
                $request->get('birthday'),
                $request->get('email') ?: ''
            );
            if ($result) {
                return $this->success('成功', []);
            }
            throw new \Exception('保存失败');
        } catch (ApiPlaintextException $e) {
            return $this->expire();
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getMemberInfoByOpenId(Request $request)
    {
        try {
            $param = $request->all();
            $v = Validator::make($param, [
                'open_id' => 'required',
            ], [
                'required' => 'OpenId不可为空',
            ]);

            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }

            $users = UsersService::getUserInfoByOpenid($request->get('open_id'));
            return $this->success('成功', $users);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getUserTypeByUid(Request $request)
    {
        try {
            $param = $request->all();
            $v = Validator::make($param, [
                'uid' => 'required',
            ], [
                'required' => 'uid不可为空',
            ]);

            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $type = UsersService::getUserTypeByUid($request->get('uid'));
            return $this->success('成功', compact('type'));

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getPosIdByUid(Request $request)
    {
        try {
            $param = $request->all();
            $v = Validator::make($param, [
                'uid' => 'required',
            ], [
                'required' => 'uid不可为空',
            ]);

            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $pos_id = UsersService::getPosIdByUid($request->get('uid'));
            return $this->success('成功', compact('pos_id'));

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function exportMember()
    {
        $data = UsersService::exportMember();
        return $this->success('OK', $data);
    }

    public function getBalance(Request $request)
    {
        try {
            $params = $request->all();
            if (empty($params['user_id'])) {
                $params['user_id'] = $this->getUid();
            }
            $userInfo = UsersService::getUserInfo($params['user_id']);
            return $this->success('success', ['balance' => $userInfo['balance']]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), []);
        }
    }
    public function removeAccount(Request $request)
    {
        try {
            Users::query()->where('phone','=','18310169947')->delete();
            return $this->success();
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), []);
        }
    }
}
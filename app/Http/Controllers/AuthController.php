<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request){
        
        $validator = Validator::make($request->all(),[
             'name'=>'required|string|between:2,100',
             'email'=> 'required|email|unique:users',
             'password'=>'required|min:5'
        ]);
         if($validator->fails()){
             return response()->json([
                 $validator->errors()
             ], 422);
         }
    
         $user = User::create(array_merge(
         $validator->validated(),
        ['password'=>bcrypt($request->password)] 
         ));
    
         return response()->json(['message'=>'User created successfully', 'user'=>$user]);
    
      } 

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json(auth()->user());
    }
    
    public function update(Request $request, User $user)
    {
        $validator= Validator::make($request->all(),[
            'name'=>'required|string',
            'email'=>'required|string',
            'password'=>'required|min:5'
        ]);
         
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors' => $validator->errors()
            ], 400);
        }
   
        
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();
        if( $user->save()){
            return response()->json([
                'status'=>true,
                'user'=>$user
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message'=> 'Oops, the user could not be updated'
            ]);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
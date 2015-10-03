package com.techstersolutions.answerme;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.drawable.BitmapDrawable;
import android.media.MediaPlayer;
import android.net.Uri;
import android.os.AsyncTask;
import android.util.Log;
import android.view.ViewGroup;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.MediaController;
import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.VideoView;

import com.daimajia.androidanimations.library.Techniques;
import com.daimajia.androidanimations.library.YoYo;

import org.json.JSONException;

import java.util.HashMap;


public class LoginActivity extends ActionBarActivity {
    Button signInButton;
    EditText regEmail, regPass;
    TextView regForgot, regCreate;
    ImageView bg, logo;
    YoYo.AnimationComposer fadeInAnimation, shakeAnimation;
    Thread initialAnimationThread;
    CheckBox autoLoginBox;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        signInButton = (Button) findViewById(R.id.signInButton);
        regEmail = (EditText) findViewById(R.id.reg_email);
        regPass = (EditText) findViewById(R.id.reg_pass);
        bg = (ImageView) findViewById(R.id.bg);
        logo = (ImageView) findViewById(R.id.logo);
        regForgot = (TextView) findViewById(R.id.reg_forgot);
        regCreate = (TextView) findViewById(R.id.reg_create);
        autoLoginBox = (CheckBox) findViewById(R.id.checkBox);

        fadeInAnimation = YoYo.with(Techniques.FadeIn).duration(700);
        shakeAnimation = YoYo.with(Techniques.Shake).duration(700);

        logo.setVisibility(View.INVISIBLE);
        regEmail.setVisibility(View.INVISIBLE);
        regPass.setVisibility(View.INVISIBLE);
        signInButton.setVisibility(View.INVISIBLE);
        regForgot.setVisibility(View.INVISIBLE);
        regCreate.setVisibility(View.INVISIBLE);

        SharedPreferences sharedPreferences = this.getSharedPreferences(
                "com.techstersolutions.answerme", Context.MODE_PRIVATE);
        if(sharedPreferences.getBoolean("autologin", false)) {
            regEmail.setText(sharedPreferences.getString("username", "ERR"));
            regPass.setText(sharedPreferences.getString("password", "ERR"));
            loginToApp();
        }
        signInButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                loginToApp();
            }
        });

        initialAnimationThread = new Thread(new Runnable() {
            @Override
            public void run() {
                try {
                    Thread.sleep(1000);
                    runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            logo.setVisibility(View.VISIBLE);
                            fadeInAnimation.playOn(logo);

                            fadeInAnimation = YoYo.with(Techniques.FadeIn).duration(700);
                        }
                    });
                    Thread.sleep(1500);
                    runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            regEmail.setVisibility(View.VISIBLE);
                            regPass.setVisibility(View.VISIBLE);
                            signInButton.setVisibility(View.VISIBLE);

                            fadeInAnimation.playOn(regEmail);
                            fadeInAnimation.playOn(regPass);
                            fadeInAnimation.playOn(signInButton);

                            fadeInAnimation = YoYo.with(Techniques.FadeIn).duration(700);
                        }
                    });
                    Thread.sleep(1500);
                    runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            regForgot.setVisibility(View.VISIBLE);
                            regCreate.setVisibility(View.VISIBLE);

                            fadeInAnimation.playOn(regForgot);
                            fadeInAnimation.playOn(regCreate);

                            fadeInAnimation = YoYo.with(Techniques.FadeIn).duration(700);
                        }
                    });
                } catch (InterruptedException e) {
                    e.printStackTrace();
                }

            }
        });
        initialAnimationThread.start();
    }

    private void loginToApp() {
        if(isEmpty(regEmail) || isEmpty(regPass)) {
            shakeAnimation.playOn(regEmail);
            shakeAnimation = YoYo.with(Techniques.Shake).duration(700);
            shakeAnimation.playOn(regPass);
        } else {
            if(autoLoginBox.isChecked()) {
                saveLogin(regEmail.getText().toString(), regPass.getText().toString(), true);
            }
            regEmail.setEnabled(false);
            regPass.setEnabled(false);
            signInButton.setEnabled(false);
            ((BitmapDrawable) bg.getDrawable()).getBitmap().recycle();
            finish();
            new AsyncHandler().execute(regEmail.getText().toString(), regPass.getText().toString());
            startActivity(new Intent(getApplicationContext(), QuestionListActivity.class));
            overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
        }
    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_registration, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();

        //noinspection SimplifiableIfStatement
        if (id == R.id.action_settings) {
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    static void unbindDrawables(View view) {
        try{
            System.out.println("UNBINDING"+view);
            if (view.getBackground() != null) {

                ((BitmapDrawable)view.getBackground()).getBitmap().recycle();


                view.getBackground().setCallback(null);
                view=null;
            }

            if (view instanceof ViewGroup) {
                for (int i = 0; i < ((ViewGroup) view).getChildCount(); i++) {
                    unbindDrawables(((ViewGroup) view).getChildAt(i));
                }
                ((ViewGroup) view).removeAllViews();
            }

        }catch (Exception e) {
            // TODO: handle exception
            e.printStackTrace();
        }
    }

    private boolean isEmpty(EditText etText) {
        return etText.getText().toString().trim().length() == 0;
    }

    private class AsyncHandler extends AsyncTask<String, Void, Void>
    {

        @Override
        protected void onPreExecute() {
            super.onPreExecute();

        }

        @Override
        protected Void doInBackground(String... params) {
            try {
                DbHandler.getSession(getApplicationContext(), params[0], params[1]);
            } catch (JSONException e) {
                e.printStackTrace();
            }
            return null;

        }

        protected void onPostExecute(Void result) {

        }
    }

    private void saveLogin(String username, String pass, boolean autoLogin) {
        SharedPreferences sharedPreferences = this.getSharedPreferences(
                "com.techstersolutions.answerme", Context.MODE_PRIVATE);
        sharedPreferences.edit().putString("username", username).apply();
        sharedPreferences.edit().putString("password", pass).apply();
        sharedPreferences.edit().putBoolean("autologin", autoLogin).apply();
        sharedPreferences.edit().commit();
    }

}

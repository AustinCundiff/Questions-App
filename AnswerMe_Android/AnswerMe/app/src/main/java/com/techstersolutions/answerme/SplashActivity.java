package com.techstersolutions.answerme;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Build;
import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.Toast;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.params.BasicHttpParams;
import org.apache.http.util.EntityUtils;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;


public class SplashActivity extends Activity {
    public static final String ROOT_ADDR = "http://dev.qa.switchit001.com/dev2/request.php";
    public static final int MSG_ID = 1;

    String sessionKey;
    SharedPreferences sharedPrefs;
    JSONObject jsonObject;
    String url;
    Intent intent;

    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);
        int currentapiVersion = android.os.Build.VERSION.SDK_INT;
        if (currentapiVersion >= Build.VERSION_CODES.LOLLIPOP){
            getWindow().addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);

            getWindow().setStatusBarColor(getResources().getColor(R.color.primaryColor));
            // Do something for froyo and above versions
        }

        intent = new Intent(this, LoginActivity.class);
        ImageView imgview = (ImageView)findViewById(R.id.imageView1);
        imgview.setScaleType(ImageView.ScaleType.FIT_XY);


        /*
        if(sessionKey exists) {
            start QuestionListActivity
        } else {
            start LoginActivity
        }
         */
    }

//    private class AsyncHandler extends AsyncTask<String, Void, Void>
//    {
//
//        @Override
//        protected void onPreExecute() {
//            super.onPreExecute();
//
//        }
//
//        @Override
//        protected Void doInBackground(String... params) {
//            DbHandler.getSessionKey(getApplicationContext(), params);
//            return null;
//
//        }
//
//        protected void onPostExecute(Void result) {
//            startActivity(intent);
//            SplashActivity.this.finish();
//        }
//    }
}


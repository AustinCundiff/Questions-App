package com.techstersolutions.answerme;

import java.io.ByteArrayOutputStream;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.ProtocolException;
import java.net.URL;

//import android.app.Activity;
import android.os.Build;
import android.support.v7.app.ActionBarActivity;
import android.support.v7.widget.Toolbar;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.graphics.Bitmap.CompressFormat;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Spinner;
import android.widget.Toast;

import com.techstersolutions.answerme.QuestionListActivity;

public class MsgActivity extends ActionBarActivity {
    public static final String ROOT_ADDR = "http://dev.qa.switchit001.com/dev2/request.php";
    public static final int MSG_ID = 1;
    public static final String BOUNDARY = "*****";
    public static final String LINE_END= "\r\n";
    public static final String TWO_HYPHENS="--";
    SharedPreferences sharedPrefs;
    EditText titleField, msgField;
    Spinner dropdownMenu;
    Button submitButton, skipButton;
    ImageView imgView;
    String url, sessionKey;
    Bitmap bitmap;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_msg);
/************************************/
        int currentapiVersion = android.os.Build.VERSION.SDK_INT;
        if (currentapiVersion >= Build.VERSION_CODES.LOLLIPOP){
            getWindow().addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);

            getWindow().setStatusBarColor(getResources().getColor(R.color.primaryColor));
            // Do something for froyo and above versions
        }
        Toolbar toolbar = (Toolbar)findViewById(R.id.app_bar);
     if (toolbar != null) {
         setSupportActionBar(toolbar);

         toolbar.setNavigationIcon(R.drawable.homeicon);
         toolbar.setTitle("home");
        }





        dropdownMenu = (Spinner) findViewById(R.id.spinner1);
        submitButton = (Button) findViewById(R.id.submitButton);
        skipButton = (Button) findViewById(R.id.skipButton);
        imgView = (ImageView) findViewById(R.id.pic_view);
        titleField = (EditText) findViewById(R.id.title_field);
        msgField = (EditText) findViewById(R.id.msg_field);
/************************************/
        sharedPrefs = this.getSharedPreferences("com.switchit001.qaapp", Context.MODE_PRIVATE);
        sessionKey = sharedPrefs.getString("sessionKey", "NULL");

        bitmap = PicUtil.loadFromCacheFile();
        if(bitmap == null)
            Log.e("QA_APP", "NULL :(");
        imgView.setImageBitmap(bitmap);

        submitButton.setOnClickListener(new OnClickListener() {

            @Override
            public void onClick(View v) {
                if(titleField.getText().toString().matches("") || msgField.getText().toString().matches(""))
                    Toast.makeText(getApplicationContext(), "Please fill in both fields", Toast.LENGTH_SHORT).show();
                else {
                    String title = titleField.getText().toString().replaceAll(" ", "%20");
                    String msg = msgField.getText().toString().replaceAll(" ", "%20");
                    url = ROOT_ADDR
                            + "?session=" + sessionKey
                            + "&msgId=2"
                            + "&par0=" + title
                            + "&par1=1"
                            + "&par2=" + msg
                            + "&par3=1";
                    Log.d("QA_APP", url);
                    new AsyncHandler().execute(url);
                }
            }
        });

        skipButton.setOnClickListener(new OnClickListener() {

            @Override
            public void onClick(View v) {
                startActivity(new Intent(getApplicationContext(), QuestionListActivity.class));
            }
        });


    }
    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_msg, menu);
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
        if(id==android.R.id.home){
            startActivity(new Intent(getApplicationContext(), QuestionListActivity.class));//NavUtils.navigateUpFromSameTask(this);
        }
        return super.onOptionsItemSelected(item);
    }
    private class AsyncHandler extends AsyncTask<String, Void, Void>
    {

        @Override
        protected void onPreExecute() {
            super.onPreExecute();

        }

        @SuppressWarnings("deprecation")
        @Override
        protected Void doInBackground(String... params) {

            String urlString=params[0];//this holds the URL location in the text format
            URL url;
            HttpURLConnection connection=null;
            DataOutputStream outputStream=null;
            DataInputStream inputStream=null;

            byte[] serverOutput=new byte[100];//temporarily holds server output before its converted
            String serverOutputString="";//Permanently holds the server output
            int serverOutputBytesToRead;//holds the number of bytes that need to be read from the server

/*
* Alternative method to load file. This is better to use
* if you plan to save the file in the cache.
*
FileInputStream fileInput=null;
try {
fileInput=new FileInputStream(new File(PictUtil.getCacheFilename()));
} catch (FileNotFoundException e1) {
// TODO Auto-generated catch block
e1.printStackTrace();
}
*/

//set up the URL and connection
            try{
                url=new URL(urlString);
                connection=(HttpURLConnection)url.openConnection();
            }
            catch (MalformedURLException e){
                Log.e("Error","Malformed URL",e);
            }
            catch (IOException e){
                Log.e("Error","IOError while establishing connection",e);
            }


//set up the connection to upload a file
            try{
                connection.setDoInput(true);
                connection.setDoOutput(true);
                connection.setUseCaches(false);
                connection.setRequestMethod("POST");
                connection.setRequestProperty("Connection", "Keep-Alive");
                connection.setRequestProperty("Content-Type", "multipart/form-data;boundary="+BOUNDARY);
            }
            catch (ProtocolException e){
                Log.e("Error","Protocol error while setting the connection properties",e);
            }

//Send the file
            try{
                outputStream = new DataOutputStream (connection.getOutputStream());
                outputStream.writeBytes(TWO_HYPHENS+BOUNDARY+LINE_END);
                outputStream.writeBytes("Content-Disposition: form-data; name=\"file0\";filename=\"upload.jpg\""+LINE_END);
                outputStream.writeBytes(LINE_END);
            }
            catch (IOException e){
                Log.e("Error","IOError while preparing to send file",e);
            }


//send the file
            try{
                ByteArrayOutputStream bos = new ByteArrayOutputStream();
                bitmap.compress(CompressFormat.JPEG, 100, bos);
                byte[] data = bos.toByteArray();
                outputStream.write(data,0,data.length);

/*
* Alternative method to load the file. This method should be
* used if you plan to use the cache.
*
* byte[] buffer=new byte[100];
int bytesRead=fileInput.read(buffer,0,100);
while (bytesRead>0)
{
outputStream.write(buffer,0,bytesRead);
bytesRead=fileInput.read(buffer,0,100);
}*/
            }
            catch (IOException e){
                Log.e("Error","IOError while sending file to server",e);
            }

//write the post file upload info
            try{
                outputStream.writeBytes(LINE_END);
                outputStream.writeBytes(TWO_HYPHENS+BOUNDARY+TWO_HYPHENS+LINE_END);
                outputStream.flush();
                outputStream.close();
            }
            catch (IOException e){
                Log.e("Error","Error while writting post file upload info",e);
            }


//read the server's response
            try{
                inputStream=new DataInputStream(connection.getInputStream());

                serverOutputBytesToRead=inputStream.available();

                while (serverOutputBytesToRead>0)
                {
                    if (serverOutputBytesToRead>100)
                    {
                        serverOutputBytesToRead=100;
                    }

                    inputStream.read(serverOutput, 0,serverOutputBytesToRead);
                    serverOutputString+=new String(serverOutput,"UTF-8");

                    serverOutputBytesToRead=inputStream.available();
                }
            }catch (IOException e)
            {
                Log.d("Error", "IOError while reading from server", e);
            }

            return null;

        }

        protected void onPostExecute(Void result) {
            startActivity(new Intent(getApplicationContext(), QuestionListActivity.class));
        }
    }



}
package com.techstersolutions.answerme;

import android.content.Context;
import android.content.SharedPreferences;
import android.graphics.BitmapFactory;
import android.util.Log;
import android.widget.Toast;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.ParseException;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicHeader;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.params.BasicHttpParams;
import org.apache.http.protocol.HTTP;
import org.apache.http.util.EntityUtils;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.net.MalformedURLException;
import java.net.URISyntaxException;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;

/**
 * Created by Jason John on 5/9/15.
 */
public class DbHandler {
    public static final String ROOT_ADDR = "http://dev3.answermeapp.com/request.php";
    public static final String TAG = "QA_APP";

    public static String getSession(Context context, String username, String pass) throws JSONException {
        String sessionKey = null;
        String encryptedPass = null;
        String URL = "";

        URL = ROOT_ADDR;
        JSONObject jsonToPost = new JSONObject();
        try {
            jsonToPost.put("msgId", 1);
            jsonToPost.put("ver", 1);
            jsonToPost.put("username", username);
            jsonToPost.put("pass", pass);
            jsonToPost.put("debugOn", 1);
        } catch (JSONException e) {
            e.printStackTrace();
        }

        JSONObject returnJson = postJson(URL, jsonToPost);
        returnJson = returnJson.getJSONObject("results");
        sessionKey = returnJson.getString("sessionKey");

        //throw session key into shared prefs
        SharedPreferences sharedPreferences = context.getSharedPreferences(
                "com.techstersolutions.answerme", Context.MODE_PRIVATE);
        sharedPreferences.edit().putString("sessionId", sessionKey).apply();
        sharedPreferences.edit().commit();

        Log.d(TAG, "New Session Key: " + sessionKey);


        Log.d(TAG, returnJson.toString());
        //extract sessionKey
        return sessionKey;
    }

    public static JSONObject postJson(String urlString, JSONObject json) {
        URL url;
        JSONObject responseJson = null;
        HttpClient client = null;
        HttpPost httpPost = null;
        StringEntity se = null;
        HttpResponse response = null;
        List<NameValuePair> nameValuePairs = null;
        try {
            url = new URL(urlString);
            client = new DefaultHttpClient();
            httpPost = new HttpPost(url.toURI());
//            se = new StringEntity(json.toString(), HTTP.UTF_8);
//            se.setContentEncoding(new BasicHeader(HTTP.CONTENT_TYPE, "multipart/form-data"));
            nameValuePairs = new ArrayList<NameValuePair>(2);
            nameValuePairs.add(new BasicNameValuePair("json", json.toString()));

            httpPost.setEntity(new UrlEncodedFormEntity(nameValuePairs));
        } catch (MalformedURLException e) {
            e.printStackTrace();
        } catch (URISyntaxException e) {
            e.printStackTrace();
        } catch (UnsupportedEncodingException e) {
            e.printStackTrace();
        }
        //		httpPost.setEntity(new StringEntity(json.toString(), "UTF-8"));
        //		httpPost.setHeader("Content-Type", "application/json");
        //		httpPost.setHeader("Accept-Encoding", "application/json");
        //		httpPost.setHeader("Accept-Language", "en-US");
        //		httpPost.addHeader("Content-Length", Double.toString(json.length())); //length returns # of keys
//        httpPost.setEntity(se);


        try {
            response = client.execute(httpPost);

        } catch (IOException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
        }
        try {
            responseJson = new JSONObject(EntityUtils.toString(response.getEntity()).toString());
//			System.out.println(responseJson.toString());
        } catch (ParseException | JSONException | IOException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();

        }
        return responseJson;
    }

    public static ArrayList<QuestionObject> getQuestionList(Context context, String searchParam, int sortOrder)
            throws JSONException, IOException {
        ArrayList<QuestionObject> list = new ArrayList<>();
        SharedPreferences sharedPreferences = context.getSharedPreferences(
                "com.techstersolutions.answerme", Context.MODE_PRIVATE);
        String URL = ROOT_ADDR;
        JSONObject jsonToPost = new JSONObject();
        try {
            jsonToPost.put("session", sharedPreferences.getString("sessionId", "ERR"));
            jsonToPost.put("msgId", 3);
//            jsonToPost.put("category", 1);
//            jsonToPost.put("search", searchParam);
            jsonToPost.put("sortOrder", sortOrder);
            jsonToPost.put("continuePrev", 0);

            Log.d(TAG, jsonToPost.toString());
        } catch (JSONException e) {
            e.printStackTrace();
        }

        JSONObject returnJson = postJson(URL, jsonToPost);
        returnJson = returnJson.getJSONObject("results");
        JSONArray jArr = returnJson.getJSONArray("questions");
        for(int i = 0; i < jArr.length(); i++) {
            JSONObject temp = jArr.getJSONObject(i);

            QuestionObject questionObject = new QuestionObject();
            questionObject.setId(temp.getInt("id"));
            questionObject.setCategory(temp.getInt("category"));
            questionObject.setTitle(temp.getString("title"));
//            questionObject.setThumbUrl(temp.getString("thumbnail"));
            Log.d(TAG, questionObject.getThumbUrl());
//            questionObject.setThumbnail(PicUtil.getBitmapFromURL(questionObject.getThumbUrl()));
            list.add(questionObject);
        }

        return list;
    }


}


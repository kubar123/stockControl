package com.example.aaaaaaaaaaa;

import android.app.Activity;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.webkit.WebChromeClient;
import android.webkit.WebView;


public class MainActivity extends Activity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
    	//Remove title bar
//    	this.requestWindowFeature(Window.FEATURE_NO_TITLE);
//
//    	//Remove notification bar
//    	this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);

    	
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        
        WebView webview = (WebView) findViewById(R.id.webView1);
        webview.setWebChromeClient(new WebChromeClient());//alerts
        webview.getSettings().setJavaScriptEnabled(true);
        webview.getSettings().setDomStorageEnabled(true);//local storage
        webview.getSettings().setDatabasePath("/data/data/" + webview.getContext().getPackageName() + "/databases/");//save local storage
        webview.loadUrl("file:///android_asset/index.html");
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();
        if (id == R.id.action_settings) {
            return true;
        }
        return super.onOptionsItemSelected(item);
    }
}

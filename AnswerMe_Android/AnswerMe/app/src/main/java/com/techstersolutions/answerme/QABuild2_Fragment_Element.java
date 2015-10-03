package com.techstersolutions.answerme;

import android.app.Activity;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;


public class QABuild2_Fragment_Element extends Fragment {
    public static final String EXTRA_MESSAGE = "DATA";
    public QData mData;
    public static final QABuild2_Fragment_Element newInstance(QData data)
    {
        QABuild2_Fragment_Element f = new QABuild2_Fragment_Element();
        Bundle bdl = new Bundle(1);
        bdl.putSerializable(EXTRA_MESSAGE, data);
        f.setArguments(bdl);
        return f;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        mData = (QData) getArguments().getSerializable(EXTRA_MESSAGE);
        View v = inflater.inflate(R.layout.fragment_qabuild2, container, false);
        RelativeLayout rl = (RelativeLayout) v.findViewById(R.id.qabuild2_fragment_rl);
        RelativeLayout.LayoutParams params = new RelativeLayout.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT);
        params.addRule(RelativeLayout.CENTER_IN_PARENT, RelativeLayout.TRUE);


        if(mData.isText()) {
            TextView tv = new TextView(getActivity().getApplicationContext());
            tv.setText(mData.getText());
            rl.addView(tv, params);
        } else if(!mData.isText()) {
            ImageView iv = new ImageView(getActivity().getApplicationContext());
            byte[] data = mData.getImage();
            iv.setImageBitmap(BitmapFactory.decodeByteArray(data, 0, data.length));
            rl.addView(iv, params);
        }


        return v;
    }

}
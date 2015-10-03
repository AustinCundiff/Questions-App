package com.techstersolutions.answerme;

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;

/**
 * Created by Aust1_000 on 3/17/2015.
 */
public class PreviewFragment extends Fragment {
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.qabuilder_fragment_layout,
                container, false);
        //Button button = (Button) view.findViewById(R.id.button8);
        return view;

    }
}
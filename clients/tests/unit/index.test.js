import * as cfStateWebpack from '@caldera-labs/state';
import {registerStore,withSelect} from "@wordpress/data";
import React from 'react';


describe( 'Testing', ()=> {
    it( 'Works', ()=> {
        const roy= true;
        expect(roy).toBe(roy);
    });

    it( 'Can snapshot', ()=> {
        const mike = {
            roy:true
        };
        expect(JSON.stringify(mike)).toMatchSnapshot();
    });


});

describe( 'Dependencies are available', () => {
    describe( 'Caldera State', () => {
        it( 'Can use imported state',  () => {
            expect(cfStateWebpack.store.actions.setForm({name:1})).toEqual({ type: 'SET_FORM', form: { name: 1 } });
        });

        it( 'Can import CF state', () => {
            expect(cfStateWebpack).toHaveProperty('store');
            expect(cfStateWebpack).toHaveProperty('state');
        });

    });

    describe( 'wp.data', () => {
        it( 'registerStore is available',  () => {
            expect(typeof registerStore ).toEqual('function');
        });

        it( 'withSelect is available',  () => {
            expect(typeof withSelect ).toEqual('function');
        });


        it( 'registerStore works',  () => {
            const initalState = {
                hi: 'Roy'
            };
            expect( registerStore( 'testStore', {
                reducer( state = initalState, action ) {
                    //reduce nothing
                    return state;
                },
            }).getState() ).toEqual(initalState);
        });
        
    });

});

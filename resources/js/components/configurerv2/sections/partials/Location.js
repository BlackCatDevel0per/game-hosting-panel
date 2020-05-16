import React from 'react';
import feather from "feather-icons";
import icons from "../../icons";
import tailwind from "../../tailwind";

const Wrapper = tailwind.div(({available, selected}) => `
    trans relative flex flex-row items-center
    px-6 py-4 border ${selected ? 'border-blue-600 bg-blue-100 shadow' : ''}
    rounded ${available ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed'}
`);

const SelectionCheck = tailwind.div(({selected}) => `
    trans m-2 absolute text-white top-0 right-0
    bg-blue-600 ${selected ? 'opacity-100' : 'opacity-0'}
    rounded-full shadow
`);

const Flag = tailwind.div(() => `
    w-16 h-16
`);

const Info = tailwind.div(({selected}) => `
    ml-4 flex-grow ${selected ? 'text-blue-700' : ''}
`);

const Short = tailwind.div(() => `
    text-xl font-bold
`);

const Long = tailwind.div(() => `
    text-sm font-light tracking-tight
`);

export default function Location({id, location, selected, onClick}) {
    const {short, long, flag, available} = location;

    function getWrapperOnClick() {
        return available ? onClick.bind(this, id) : undefined;
    }

    function getWrapperStyle() {
        return {
            filter: available ? '' : 'grayscale(100%)'
        };
    }

    return <Wrapper
        $onClick={getWrapperOnClick()}
        selected={selected}
        available={available}
        $style={getWrapperStyle()}
    >
        <SelectionCheck
            selected={selected}
            $dangerouslySetInnerHTML={{__html: feather.icons.check.toSvg()}}
        />

        <Flag>
            <img alt={`${short} | ${long}`} src={icons[flag]}/>
        </Flag>

        <Info>
            <Short>{short}</Short>
            <Long>{long}</Long>
        </Info>
    </Wrapper>
}

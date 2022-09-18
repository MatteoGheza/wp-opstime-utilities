import { get } from 'lodash';
import {
	RadioControl,
	SelectControl
} from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';

/**
 * Module Constants
 */

// Set to false to use radio buttons instead
const USE_SELECT_CONTROL = true;

const DEFAULT_QUERY = {
	per_page: -1,
	orderby: 'name',
	order: 'asc',
	_fields: 'id,name,parent',
	context: 'view',
};

const EMPTY_ARRAY = [];

function OneTermSelector({ slug }) {
	const { editEntityRecord } = useDispatch('core');

	const {
		hasAssignAction,
		terms,
		availableTerms,
		taxonomy,
		postType,
		postId,
	} = useSelect(
		(select) => {
			const { getCurrentPost, getEditedPostAttribute } =
				wp.data.select('core/editor');
			const { getTaxonomy, getEntityRecords, isResolving } =
				select(coreStore);
			const _taxonomy = getTaxonomy(slug);
			const {
				getCurrentPostType,
				getCurrentPostId,
			} = select('core/editor');

			return {
				hasAssignAction: _taxonomy
					? get(
						getCurrentPost(),
						[
							'_links',
							'wp:action-assign-' + _taxonomy.rest_base,
						],
						false
					)
					: false,
				terms: _taxonomy
					? getEditedPostAttribute(_taxonomy.rest_base)
					: EMPTY_ARRAY,
				availableTerms:
					getEntityRecords('taxonomy', slug, DEFAULT_QUERY) ||
					EMPTY_ARRAY,
				taxonomy: _taxonomy,
				postType: getCurrentPostType(),
				postId: getCurrentPostId(),
			};
		},
		[slug]
	);

	if (!hasAssignAction) {
		return null;
	}

	const onChange = (termID) => {
		let postUpdate = {};
		postUpdate[taxonomy.rest_base] = [parseInt(termID, 10)];
		editEntityRecord('postType', postType, postId, postUpdate);
	}

	const renderTerms = (renderedTerms) => {
		if (USE_SELECT_CONTROL) {
			return (
				<SelectControl
					value={
						terms[0]
					}
					onChange={
						(selectedValue) => {
							onChange(selectedValue);
						}
					}
					options={
						renderedTerms.map((term) => {
							return {
								value: term.id,
								label: term.name
							};
						})
					}
				/>
			);
		} else {
			return (
				<RadioControl
					selected={
						terms[0]
					}
					onChange={
						(selectedValue) => {
							onChange(selectedValue);
						}
					}
					options={
						renderedTerms.map((term) => {
							return {
								value: term.id,
								label: term.name
							};
						})
					}
				/>
			);
		}
	}

	return (
		<>
			<div
				className="editor-post-taxonomies__hierarchical-terms-list"
				tabIndex="0"
				role="group"
				aria-label={ taxonomy.name }
			>
				{ renderTerms(availableTerms) }
			</div>
		</>
	);
}

function customizeTaxonomySelector(OriginalComponent) {
	return function (props) {
		if (!props) {
			return <OriginalComponent {...props
			}
			/>;
		}
		if (props.slug === 'edizione') {
			return <OneTermSelector {...props
			}
			/>;
		}
		return <OriginalComponent {...props
		}
		/>;
	};
}
wp.hooks.addFilter('editor.PostTaxonomyType', 'opstime-utilities', customizeTaxonomySelector);
